<template>
    <div class="bg-root h-screen">
        <div class="wrapper-nav w-screen">
            <a href="/"
                ><p class="font-condensed text-2xl"><u>HOME</u></p></a
            >
            <img
                src="/assets/FinnHarmensLogo.png"
                alt="Logo"
                class="absolute right-4" />
        </div>

        <div class="header">
            <div class="name">
                <div class="name-image">
                    <img
                        src="/assets/Barcelona.jpeg"
                        alt="Barcelona"
                        class="fade-in" />
                </div>

                <div class="name-text">
                    <h1
                        class="flex items-center justify-center text-center h-screen">
                        <span>FINN<br />HARMENS</span>
                    </h1>
                </div>
            </div>
        </div>
        <h2>Hey</h2>
    </div>

</template>

<style>
.bg-root {
    background: #f37c90;
    min-height: 100vh;
}

h1 {
    font-family: "Roboto Mono";
    color: #212121;
    font-size: 280px;
    line-height: 1;
    font-weight: 700;
    margin-bottom: 0;
}

.font-condensed {
    font-family: "Roboto Condensed";
}

.wrapper-nav {
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    height: 50px;
    padding-left: 20px;
    width: 100%;
    background: transparent;
    z-index: 10;
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

.name-text {
    position: relative;
    z-index: 1;
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
