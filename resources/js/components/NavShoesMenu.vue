<template>
<nav class="mb-2 mt-1 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div class="d-flex justify-content-center align-items-center gep-2">
        <a :href="r('shoes.group')" class="btn btn-primary">
            Групи взуття
        </a>
    </div>
    <a :href="r('shoes.models')" class="btn btn-primary">
        Моделі взуття
    </a>
    <a :href="r('shoes.sales')" class="btn btn-primary">
        Продажі взуття
    </a>
    <a :href="r('shoes.warehouse')" class="btn btn-primary">
        Склад взуття
    </a>
</nav>
</template>
<script setup>
const r = p => route(p);

</script>
