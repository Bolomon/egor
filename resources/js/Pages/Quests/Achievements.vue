<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";

</script>

<template>
    <Head title="Достижения и награды" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Достижения и награды</h1>
            <div class="info-block">
                <p>
                    С актерами всегда так: факты путают, титулы приписывают, заслуги преуменьшают.<br>
                    А что было на самом деле?<br>
                    <br>
                    Перед вами 5 утверждений о Табакове.<br>
                    Отметьте, что из этого правда, а что ложь.
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
