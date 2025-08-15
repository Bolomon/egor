<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="Роли и образы" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Роли и образы</h1>
            <div class="info-block">
                <p>
                    Перед вами театральные образы Олега Павловича. Но чего-то здесь не хватает?...
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
