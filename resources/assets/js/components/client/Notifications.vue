<template>
    <div >
        <div class="col-md-8">
            <div class="alert alert-success" v-if="success.length > 0" >
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <p>
                    <strong>   {{ success}} </strong>
                </p>
            </div>
            <div class="alert alert-danger" v-if="deleted.length > 0" >
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <p>
                    <strong>   {{ deleted}} </strong>
                </p>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <button v-if="sms === 0"  class="btn disabled" v-tooltip="'Buy sms'"><i class="fa fa-plus"></i> Add Item</button>
                    <button  @click="showCreateForm" class="btn btn-primary btn-sm" v-else><i class="fa fa-plus"></i> Add Item</button>
                </div>
                <div class="col-md-9">
                    <div class="input-group ">
                        <input type="text" class="form-control  no-border"  v-model="search" placeholder="Search">
                        <span class="input-group-btn">
                            <button style="background-color: #fff;"  class="btn btn-default no-border" type="button"><span class="glyphicon glyphicon-search"></span></button>
                        </span>
                    </div>
                </div>
                <!--<div class="col-md-9">
                    <div class="input-group ">
                        <div class="input-group-btn search-panel col-xm-3">
                                <span>
                                    <select class="form-control search-select-box no-border" v-model="search_param" >
                                        <option value="products">Items</option>
                                        <option value="suppliers">Sellers</option>
                                    </select>
                                </span>
                        </div>
                        <input type="text" class="form-control  no-border"  v-model="search" placeholder="Search"  @keyup.enter="doSearch">
                        <span class="input-group-btn">
                            <button style="background-color: #fff;" v-on:click="doSearch" class="btn btn-default no-border" type="button"><span class="glyphicon glyphicon-search"></span></button>
                        </span>
                    </div>
                </div>-->
            </div>
            <div class="row">
                <strong class="m-b-none text-center" v-if="notifications.length === 0">
                    No notifications found.
                </strong>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                loadin:false,
                loading:false,
                deleted: [],
                success: [],
                notifications: [],

            };
        },


        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getNotifications()

            },


            /**
             * delete the given notification.
             */
            revoke(item){
                this.$dialog.confirm('Are you sure ', { loader: true })
                    .then((dialog) => {
                        setTimeout(() => {
                            axios.post('/client/delete-notification/' + item.id )
                                .then(response => {
                                    this.deleted = response.data;
                                    this.getNotifications();
                                }).catch(error => {
                                if (typeof error.response.data === 'object') {
                                    this.deleted = 'Something went wrong refresh this page';
                                } else {
                                    this.deleted = 'Something went wrong refresh this page';
                                }
                            });
                            dialog.close();
                        }, 2500);
                    })
                    .catch(error => {
                        console.log('Clicked on cancel')
                    });
            },

            getNotifications(){
                axios.get('/client/get-notifications')
                    .then(response => {
                        this.notifications = response.data;
                    });
            },

        }
    }
</script>
