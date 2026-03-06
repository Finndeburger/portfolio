<script setup lang="ts">
import { useRoute } from "vue-router";

const route = useRoute();
const slug = route.params.slug as string;

// You can later pull this from your site data composable
const sites = {
  webshop: {
    title: "Webshop Project",
    content: "This was my first e-commerce demo made in Vue!",
  },
  weather: {
    title: "Weather App",
    content: "This app shows weather data using the OpenWeather API.",
  },
  portfolio: {
    title: "Portfolio Site",
    content: "The site you're looking at right now!",
  },
};

const site = sites[slug];
</script>

<template>
  <div class="p-10">
    <h1 class="text-3xl font-bold mb-4">{{ site.title }}</h1>
    <p>{{ site.content }}</p>
  </div>
</template>
