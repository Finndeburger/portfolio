<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JsonException;
use RuntimeException;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\CredentialRecord;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\Exception\InvalidDataException;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class PasskeyController extends Controller
{
    public function deleteCredential(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'credential_id' => ['required', 'string'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $credentialId = $payload['credential_id'];

        $store = $this->credentialStore($user);
        $beforeCount = count($store['credentials']);

        $credentials = collect($store['credentials'])
            ->reject(fn (array $credential) => ($credential['publicKeyCredentialId'] ?? null) === $credentialId)
            ->values()
            ->all();

        if (count($credentials) === $beforeCount) {
            return response()->json(['message' => 'Passkey credential not found.'], 404);
        }

        $user->forceFill([
            'passkey_credentials' => [
                'credentials' => $credentials,
                'updated_at' => now()->toIso8601String(),
            ],
            'passkey_enabled_at' => count($credentials) > 0 ? ($user->passkey_enabled_at ?? now()) : null,
        ])->save();

        return response()->json(['message' => 'Passkey removed successfully.']);
    }

    public function registerOptions(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $serializer = $this->serializer();

        $excludeCredentials = collect($this->credentialStore($user)['credentials'])
            ->map(function (array $credential): PublicKeyCredentialDescriptor {
                return PublicKeyCredentialDescriptor::create(
                    PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                    $this->decodeBase64Url((string) $credential['publicKeyCredentialId']),
                    $credential['transports'] ?? []
                );
            })
            ->values()
            ->all();

        $options = PublicKeyCredentialCreationOptions::create(
            PublicKeyCredentialRpEntity::create(
                (string) config('webauthn.rp_name', 'LookAtMe'),
                (string) config('webauthn.rp_id')
            ),
            PublicKeyCredentialUserEntity::create(
                $user->email,
                (string) $user->id,
                $user->name
            ),
            random_bytes(32),
            [
                PublicKeyCredentialParameters::createPk(-7),
                PublicKeyCredentialParameters::createPk(-257),
            ],
            AuthenticatorSelectionCriteria::create(
                userVerification: AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_PREFERRED,
                residentKey: AuthenticatorSelectionCriteria::RESIDENT_KEY_REQUIREMENT_PREFERRED,
            ),
            PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
            $excludeCredentials,
            (int) config('webauthn.timeout', 60000),
        );

        $normalized = $this->normalize($serializer, $options);
        $request->session()->put('passkey.register.options', $normalized);

        return response()->json(['publicKey' => $normalized]);
    }

    public function registerVerify(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'credential' => ['required', 'array'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $storedOptions = $request->session()->pull('passkey.register.options');
        if (! is_array($storedOptions)) {
            return response()->json(['message' => 'Passkey registration challenge expired. Try again.'], 422);
        }

        try {
            $serializer = $this->serializer();

            $creationOptions = $this->denormalize(
                $serializer,
                $storedOptions,
                PublicKeyCredentialCreationOptions::class
            );

            $publicKeyCredential = $this->denormalize(
                $serializer,
                $payload['credential'],
                PublicKeyCredential::class
            );

            if (! $publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
                return response()->json(['message' => 'Invalid attestation response.'], 422);
            }

            $validator = new AuthenticatorAttestationResponseValidator($this->factory()->creationCeremony());

            $credentialRecord = $validator->check(
                $publicKeyCredential->response,
                $creationOptions,
                $request->getHost()
            );

            $serializedCredential = $this->normalize($serializer, $credentialRecord);

            $store = $this->credentialStore($user);
            $credentials = collect($store['credentials'])
                ->reject(fn (array $credential) => ($credential['publicKeyCredentialId'] ?? null) === ($serializedCredential['publicKeyCredentialId'] ?? null))
                ->push($serializedCredential)
                ->values()
                ->all();

            $user->forceFill([
                'passkey_credentials' => [
                    'credentials' => $credentials,
                    'updated_at' => now()->toIso8601String(),
                ],
                'passkey_enabled_at' => now(),
            ])->save();

            return response()->json(['message' => 'Passkey registered successfully.']);
        } catch (InvalidDataException|RuntimeException|JsonException $e) {
            return response()->json(['message' => 'Passkey registration failed: '.$e->getMessage()], 422);
        }
    }

    public function loginOptions(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['nullable', 'email'],
        ]);

        $email = $payload['email'] ?? null;

        $allowCredentials = [];

        if ($email) {
            $user = User::query()->firstWhere('email', $email);

            if ($user) {
                $allowCredentials = collect($this->credentialStore($user)['credentials'])
                    ->map(fn (array $credential): PublicKeyCredentialDescriptor => PublicKeyCredentialDescriptor::create(
                        PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                        $this->decodeBase64Url((string) $credential['publicKeyCredentialId']),
                        $credential['transports'] ?? []
                    ))
                    ->values()
                    ->all();
            }
        }

        $options = PublicKeyCredentialRequestOptions::create(
            random_bytes(32),
            (string) config('webauthn.rp_id'),
            $allowCredentials,
            PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_PREFERRED,
            (int) config('webauthn.timeout', 60000)
        );

        $serializer = $this->serializer();
        $normalized = $this->normalize($serializer, $options);

        $request->session()->put('passkey.login.options', $normalized);

        return response()->json(['publicKey' => $normalized]);
    }

    public function loginVerify(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'credential' => ['required', 'array'],
        ]);

        $storedOptions = $request->session()->pull('passkey.login.options');
        if (! is_array($storedOptions)) {
            return response()->json(['message' => 'Passkey login challenge expired. Try again.'], 422);
        }

        $credentialId = (string) ($payload['credential']['id'] ?? '');
        if ($credentialId === '') {
            return response()->json(['message' => 'Missing credential id.'], 422);
        }

        [$user, $credentialData] = $this->findUserAndCredentialByCredentialId($credentialId);

        if (! $user || ! $credentialData) {
            return response()->json(['message' => 'Passkey not recognized.'], 422);
        }

        try {
            $serializer = $this->serializer();

            $requestOptions = $this->denormalize(
                $serializer,
                $storedOptions,
                PublicKeyCredentialRequestOptions::class
            );

            $publicKeyCredential = $this->denormalize(
                $serializer,
                $payload['credential'],
                PublicKeyCredential::class
            );

            if (! $publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
                return response()->json(['message' => 'Invalid assertion response.'], 422);
            }

            $record = $this->denormalize($serializer, $credentialData, CredentialRecord::class);
            $validator = new AuthenticatorAssertionResponseValidator($this->factory()->requestCeremony());

            $updatedRecord = $validator->check(
                $record,
                $publicKeyCredential->response,
                $requestOptions,
                $request->getHost(),
                $record->userHandle,
            );

            $serializedRecord = $this->normalize($serializer, $updatedRecord);

            $store = $this->credentialStore($user);
            $credentials = collect($store['credentials'])
                ->map(function (array $credential) use ($credentialId, $serializedRecord): array {
                    return ($credential['publicKeyCredentialId'] ?? null) === $credentialId
                        ? $serializedRecord
                        : $credential;
                })
                ->values()
                ->all();

            $user->forceFill([
                'passkey_credentials' => [
                    'credentials' => $credentials,
                    'updated_at' => now()->toIso8601String(),
                ],
                'passkey_enabled_at' => $user->passkey_enabled_at ?? now(),
            ])->save();

            Auth::login($user, true);
            $request->session()->regenerate();

            return response()->json(['message' => 'Passkey login successful.', 'redirect' => route('home')]);
        } catch (InvalidDataException|RuntimeException|JsonException $e) {
            return response()->json(['message' => 'Passkey login failed: '.$e->getMessage()], 422);
        }
    }

    private function serializer(): \Symfony\Component\Serializer\SerializerInterface
    {
        return (new WebauthnSerializerFactory($this->attestationManager()))->create();
    }

    private function attestationManager(): AttestationStatementSupportManager
    {
        return AttestationStatementSupportManager::create([
            new NoneAttestationStatementSupport(),
        ]);
    }

    private function factory(): CeremonyStepManagerFactory
    {
        $factory = new CeremonyStepManagerFactory();

        $factory->setAttestationStatementSupportManager($this->attestationManager());
        $factory->setAllowedOrigins((array) config('webauthn.allowed_origins', []));

        return $factory;
    }

    /**
     * @return array{credentials: array<int, array<string, mixed>>}
     */
    private function credentialStore(User $user): array
    {
        $raw = $user->passkey_credentials;

        if (is_array($raw) && isset($raw['credentials']) && is_array($raw['credentials'])) {
            return ['credentials' => $raw['credentials']];
        }

        return ['credentials' => []];
    }

    /**
     * @return array{0: null|User, 1: null|array<string, mixed>}
     */
    private function findUserAndCredentialByCredentialId(string $credentialId): array
    {
        $users = User::query()->whereNotNull('passkey_credentials')->get();

        foreach ($users as $user) {
            $credentials = $this->credentialStore($user)['credentials'];

            foreach ($credentials as $credential) {
                if (($credential['publicKeyCredentialId'] ?? null) === $credentialId) {
                    return [$user, $credential];
                }
            }
        }

        return [null, null];
    }

    private function decodeBase64Url(string $value): string
    {
        $padded = strtr($value, '-_', '+/');
        $padding = strlen($padded) % 4;

        if ($padding > 0) {
            $padded .= str_repeat('=', 4 - $padding);
        }

        return (string) base64_decode($padded, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalize(\Symfony\Component\Serializer\SerializerInterface $serializer, mixed $value): array
    {
        $json = $serializer->serialize($value, JsonEncoder::FORMAT);

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    private function denormalize(
        \Symfony\Component\Serializer\SerializerInterface $serializer,
        array $value,
        string $type
    ): mixed {
        return $serializer->deserialize(
            json_encode($value, JSON_THROW_ON_ERROR),
            $type,
            JsonEncoder::FORMAT
        );
    }
}
