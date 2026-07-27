<template>
    <div class="my-2">
        <button @click="onDelete" type="button" class="btn btn-danger">
            Видалити {{ nameItem }}
        </button>
    </div>
</template>

<script>
export default {
    props: {
        nameItem: {
            type: String,
            default: "елемент",
        },
        urlDelete: {
            type: String,
            require: true,
        },
    },
    data() {
        return {
            isError: false,
            errorMessage: "",
            isDeleted: false,
        };
    },
    methods: {
        onDelete() {
            Swal.fire({
                title: `Видалити цей ${this.nameItem}.`,
                showCancelButton: true,
                confirmButtonText: "Видалити",
            }).then((result) => {
                if (result.isConfirmed) {
                    axios
                        .delete(this.urlDelete)
                        .then((r) => {
                            if (r.status == 200) {
                                Swal.fire("Видалено", "", "success");
                            }
                        })
                        .cath((err) => {
                            Swal.fire("Сталася помилка", "", "error");
                        });
                } else if (result.isDenied) {
                    Swal.fire("Видалення скасовано", "", "info");
                }
            });
        },
    },
};
</script>
