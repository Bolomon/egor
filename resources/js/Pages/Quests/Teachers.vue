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
            <h1 class="info-title">Первые учителя</h1>
            <div class="info-block">
                <p>
                    Разговор с Орловым поставил юного Олега Табакова перед непростым выбором.<br>
                    Ему нужно было выбрать между 5 и 4 буквами. Что это были за буквы?
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
