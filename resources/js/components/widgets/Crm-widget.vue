<script>
export default {
    props: {
        title: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            required: true,
        },
        value: {
            required: true,
        },
        detail: {
            default:[],
        }
    },
    data() {
        return {};
    },
};
</script>

<template>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="text-muted font-weight-normal mt-0 text-truncate" title="Campaign Sent">{{ $t(`general.${title}`) }}</h5>
                    <h3 class="my-2 py-1">
                        <span data-plugin="counterup">{{ value }}</span>
                    </h3>
                    <template v-for="data in detail">
                        <p class="mb-0 text-muted">
                        <span class="text-success mr-2">
                            {{ data.value }}
                        </span>
                            <span class="text-nowrap">{{$t('general.'+data.name)}}</span>
                        </p>
                    </template>
                </div>
                <div class="avatar-sm">
                <span class="avatar-title bg-soft-primary rounded">
                    <i :class="`${icon} font-20`"></i>
                </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card-body {
    background-color: #bee3fe !important;
    padding: 1.6rem 10px !important;
}
.text-muted {
    color: #575b5d !important;
}

i {
    color: #0a8fc0 !important;
}
</style>

