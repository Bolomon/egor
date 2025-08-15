<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="Вехи истории" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Вехи истории</h1>
            <div class="info-block">
                <p>
                    Вы уже прошли часть выставки и без труда найдете ответы на следующие вопросы.
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
