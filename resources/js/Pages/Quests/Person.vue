<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="Грани личности" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Грани личности</h1>
            <div class="info-block">
                <p>
                    Иногда вещи могут многое рассказать<br>
                    о человеке. Если найти правильные вещи.
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
