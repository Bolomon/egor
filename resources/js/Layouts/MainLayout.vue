<script setup>

const userToken = localStorage.getItem("auth-token");

if (!userToken && window.location.pathname !== '/') {
    window.location.href = '/'
}
</script>

<template>
    <div class="main">
        <slot />
    </div>
</template>

<style scoped  lang="scss">
    .main {
        margin: 0 auto;
        position: relative;
        max-width: 350px;
        padding: 27px 0;
    }
</style>
