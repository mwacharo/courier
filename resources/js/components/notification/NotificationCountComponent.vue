<template>
    <span class="badge badge-danger">{{ notification_count}}</span>
</template>

<script>
    export default {

        mounted(){
            this.fetchNotification();
        },

        data(){
            return{
                notification_count: 0,
            }
        },

        methods: {

            fetchNotification(){
                const vm = this;
                let uri = base_url+`v1/notification-count`;
                axios.get(uri).then((response) => {
                    var count = response.data.notification_count;
                    vm.notification_count = count;
                });
            },

        }
    }
</script>
