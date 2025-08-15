<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="История жизни О. П. Табакова" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Библиотека</h1>
            <div class="info-block">
                <p>
                    Эти книги на полке у Олега Табакова были <br>
                    в числе любимых. Все, кроме 2.<br>
                    Найдите лишние книги.<br>
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
