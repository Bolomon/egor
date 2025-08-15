<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="Птицы счастья" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Птицы счастья</h1>
            <div class="info-block">
                <p>
                    Говорят, птицы принесли счастье и спрятались где-то здесь, среди декораций. Найдите всех<br>
                    и впишите их названия.
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
