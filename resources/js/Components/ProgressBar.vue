<script setup>
import {computed, onBeforeMount, ref} from "vue";

const userToken = localStorage.getItem("auth-token")

const countSteps = ref(12)
const countCompletedSteps = ref(0)

const progressPercent = computed(() => {
    return (countCompletedSteps.value / countSteps.value) * 100
})

const getCountCompletedUserSteps = () => {
    const config = {
        headers: {
            'Authorization': 'Bearer ' + userToken
        }
    }

    axios
        .get('/api/quests/completed/count', config)
        .then((data) => {
            countCompletedSteps.value = data.data.completed_quests_count
        })
}

onBeforeMount(() => {
    getCountCompletedUserSteps()
})
</script>

<template>
    <div
        class="progress"
        :style="{
            'grid-template-columns': 'repeat(' + countSteps + ', 1fr)',
            '--progress-percent': progressPercent + '%'
        }"
    >
        <div class="progress-item" v-for="(step, index) in countSteps" :key="index">
            <img
                src="/img/convert_open.svg"
                alt="completed"
                v-if="countCompletedSteps >= step"
            >
            <img
                src="/img/convert_closed.svg"
                alt="closed"
                v-else
            >
            <span
                :class="{active: countCompletedSteps >= step}"
            ></span>
        </div>
    </div>
</template>

<style scoped lang="scss">
.progress {
    position: relative;
    display: grid;
    align-items: center;

    &::before {
        content: "";
        display: block;
        left: 0;
        bottom: 2px;
        width: 100%;
        max-width: 393px;
        height: 10px;
        background-color: #313039;
        border-radius: 150px;
        position: absolute;
    }

    &::after {
        content: "";
        display: block;
        left: 0;
        bottom: 2px;
        width: var(--progress-percent, 0%);
        max-width: 393px;
        height: 10px;
        background-color: #EFC30A;
        border-radius: 150px;
        position: absolute;
    }

    &-item {
        width: 100%;
        height: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-end;
        gap: 5px;

        span {
            display: block;
            background-color: #313039;
            width: 14px;
            height: 14px;
            border-radius: 50%;

            &.active {
                background-color: #EFC30A;
            }
        }
    }
}
</style>
