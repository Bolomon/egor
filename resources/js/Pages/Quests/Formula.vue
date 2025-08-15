<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="Формула от Олега Павловича" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Формула<br>от Олега Павловича</h1>
            <div class="info-block">
                <p>
                    Формула проста:<br>
                    время + душа + здоровье + жизнь<br>
                    Секрет чего зашифрован в этой формуле?
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
