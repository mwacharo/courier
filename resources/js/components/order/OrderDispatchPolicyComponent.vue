<template>
    <div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <label>Merchant Details:</label>
                                    <select class="form-control" v-model="merchant_id">
                                        <option value="all">Select Merchant</option>
                                        <option v-for="merchant in merchants" :value="merchant.id">{{ merchant.name }}</option>
                                    </select>
                                </div>

                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8">
                                    <button class="btn btn-success mr-2" @click.prevent="generateExcel" v-if="orders.length > 0">Generate XLS</button>
                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>

                                    <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th title="Field #1">Order No</th>
                                <th title="Field #2">Sender</th>
                                <th title="Field #3">Receiver</th>
                                <th title="Field #4">Address</th>
                                <th title="Field #5">Order Status</th>
                                <th title="Field #6">Created At</th>
                                <th title="Field #7">View</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(order, index) in orders">
                                <td>{{ order.order_no }}</td>
                                <td>{{ order.sender_name }}</td>
                                <td>{{ order.receiver_name }}</td>
                                <td>{{ order.receiver_address }}</td>
                                <td>DISPATCHED</td>
                                <td>{{ order.created_at }}</td>
                                <td>
                                    <a v-bind:href='base_url_web_admin+"order-details/"+order.id'>View details</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </form>


    </div>

</template>

<script>
    export default {

        mounted(){
            this.fetchMerchants();
            this.fetchDefaultReport();
        },

        props: {
            causer_id: String,
        },

        data(){
            return{
                merchant_id: 'all',
                merchants: [],

                base_url_web_admin: '',
                orders: [],
                loader: false,
            }
        },

        methods: {

            fetchMerchants() {
                let uri = base_url + `v1/merchant-list`;
                axios.get(uri).then((response) => {
                    this.merchants = response.data;
                });
            },

            fetchDefaultReport() {
                let uri = base_url + `v1/order-dispatch-policy-default`;
                axios.get(uri).then((response) => {
                    this.orders = response.data;
                });
            },

            formSubmit(){

                this.orders = [];
                var merchant_id = this.merchant_id;

                const vm = this;
                const formData = new FormData();
                formData.append( 'merchant_id', merchant_id);

                let uri = base_url+`v1/order-dispatch-policy`;
                axios.post(uri, formData)
                    .then(function (response) {
                        vm.orders = response.data;

                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            generateExcel(){

                this.loader = true;
                if(this.orders.length == 0){
                    alert('No order items');
                    this.loader = false;
                    return
                }

                const vm = this;
                let uri = base_url + `v1/order-dispatch-policy-generate-excel`;
                axios({
                    url: uri,
                    method: 'POST',
                    responseType: 'blob',
                    data: {
                        orders: JSON.stringify(this.orders),
                        causer_id: this.causer_id,
                    }
                }).then((response) => {
                    vm.loader = false;
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'order-dispatch-policy-excel.xls');
                    document.body.appendChild(link);
                    link.click();
                });

            },

        }
    }
</script>
