<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="Кинококтейль Табакова" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Кинококтейль Табакова</h1>
            <div class="info-block">
                <p>
                    Перед вами коктейльная карта и стоп-кадры<br>
                    из 5 фильмов, в которых снимался Олег Табаков.<br>
                    <br>
                    У каждого напитка — свой характер.<br>
                    У каждого фильма — своя атмосфера.<br>
                    Сопоставьте коктейли и кадры: каким из фильмов вдохновлен каждый из напитков?
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
