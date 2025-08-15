<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="Ученики" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Ученики</h1>
            <div class="info-block">
                <p>
                    Он был не просто актером — он был учителем. Ниже — портреты его учеников.<br>
                    Всего здесь 9 учеников. Но портретов больше. Найдите 3 лишних.
                </p>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
