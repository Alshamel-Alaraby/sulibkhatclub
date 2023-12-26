<template>
    <div class="btn-group">
        <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
            style="padding: 0;">
            <i class="fa fa-exclamation"
                style="color:#fff;font-size: 13px;border-radius: 50%;background-color: #9a9a9a;padding:3px 6px"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-custom">
            <a class="dropdown-item" href="#" v-if="permissionUpdate && isEdit" @click="$emit('editRow', data_id)">
                <div class="d-flex justify-content-between align-items-center text-black">
                    <span>{{ $t("general.edit") }}</span>
                    <i class="mdi mdi-square-edit-outline text-info"></i>
                </div>
            </a>

            <a v-if="permissionDelete && isDelete" class="dropdown-item text-black" href="#"
                @click.prevent="$emit('deleteItem', data_id)">
                <div class="d-flex justify-content-between align-items-center text-black">
                    <span>{{ $t("general.delete") }}</span>
                    <i class="fas fa-times text-danger"></i>
                </div>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'permissionUpdate', 'permissionDelete', 'isEdit', 'isDelete', 'data_id',
    ],
}
</script>
