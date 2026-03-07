<script setup lang="ts">
definePageMeta({ layout: 'browser' })
</script>

<template>
    <!-- Sticky Bar -->
    <section>
        <div class="flex flex-row fixed top-5 left-0 w-screen p-10 overflow-hidden z-10">
            <h4 class="mb-4 text-[#212121] text-2xl underline">about.</h4>
        </div>
        <div class="flex flex-row justify-end fixed top-5 right-0 w-screen p-10 overflow-hidden z-10">
            <img src="/assets/FinnHarmensLogo.jpeg" alt="logo" class="w-18em mr-4" />
        </div>
    </section>

    <!-- Name + Image -->
    <section class="bg-[#f37c90]">
        <div class="header p-20">
            <div class="name">
                <div class="name-image">
                    <img src="/assets/Barcelona.jpeg" alt="Barcelona" class="fade-in" />
                </div>

                <div class="relative z-1">
                    <h1 class="flex items-center justify-center text-center h-full text-[#212121]">
                        <span>FINN<br />HARMENS</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="flex flex-row justify-between items-center p-5">
            <h4 class="text-[#212121] text-3xl">1.0</h4>
            <h4 class="text-[#212121] text-3xl">↓</h4>
        </div>
    </section>

    <!-- About -->
    <section class="bg-[#f37c90]">
        <div class="w-5/6 mx-auto flex flex-row justify-between py-20 text-[#212121]">
            <span class="border-l-2 border-[#c4455a] pl-3">About</span>
            <div>
                <p class="text-right"><span class="text-5xl">full stack web developer</span></p>
                <p class="text-right"><span class="text-5xl">game developer</span></p>
                <p class="text-right"><span class="text-5xl">app developer</span></p>
                <p class="text-right"><span class="text-5xl">database engineer</span></p>
                <p class="text-right"><span class="text-5xl">writer</span></p>
                <p class="text-right"><span class="text-5xl">worldbuilder</span></p>
                <p class="text-right"><span class="text-5xl">producer</span></p>
            </div>
        </div>
    </section>

    <!-- GitHub link -->
    <section class="bg-[#f37c90]">
        <div class="w-5/6 mx-auto flex flex-row justify-between py-20 text-[#212121]">
            <div>
                <p class="text-left"><span class="text-3xl border-r-2 text-[#FF1744] border-[#FF1744] pr-2">look
                        on</span></p>
                <a href="https://github.com/Finndeburger" class="hover:underline">
                    <div class="flex flex-row items-center gap-2">
                        <p class="text-center"><span class="text-3xl">GitHub</span></p> <img alt="github.svg"
                            src="https://raw.githubusercontent.com/lucide-icons/lucide/785b2c63769c9ab1f39b4e51048618208a74f397/icons/github.svg"
                            height="24">
                    </div>
                </a>
                <p class="text-right"><span class="text-3xl text-[#FF1744]">coming soon</span></p>
            </div>
            <span class="border-r-2 border-[#c4455a] pr-3">Portfolio</span>
        </div>
    </section>
</template>

<style scoped>
* {
    --dark: #212121;

    font-family: "Roboto Mono";
}

.bg-root {
    background: #f37c90;
    min-height: 100%;
}

h1 {
    font-size: 280px;
    line-height: 1;
    font-weight: 700;
    margin-bottom: 0;
}

.name {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.name-image {
    position: absolute;
    transform: translate3d(0px, 0px, 0px) scale(1) rotate(-3deg) skew(0deg);
    top: 0;
    left: 0;
    width: 100%;
    z-index: 2;
    display: flex;
    justify-content: center;
    align-items: center;
}

.name-image img {
    width: 350px;
    max-width: 80%;
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: rgba(255, 23, 68, 0.557) 15px 15px 5px 5px;
    opacity: 0;
    animation: fadeIn 2s ease 1s forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
