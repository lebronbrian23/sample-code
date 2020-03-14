<template>
    <div>
        <div class="alert alert-success" v-if="success.length > 0" >
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            <p>
                <strong>   {{ success}} </strong>
            </p>
        </div>
        <div class="list-group col-md-4">
            <a  @click="home" class="list-group-item  action-link">Home <i class="pull-right fa fa-home"></i></a>
            <a  @click="myItems" class="list-group-item  action-link">My Items <i class="pull-right fa fa-th"></i></a>
            <a  @click="myNotifications" class="list-group-item  action-link">Notifications <span class="badge pull-right ">{{notifications}}</span> </a>
            <a  @click="showSmsForm" class="list-group-item  action-link">Add Sms <strong class="danger" v-if="sms === 0 ">Buy SMS </strong><span class="badge pull-right ">{{sms}}</span> </a>
            <a  @click="profile" class="list-group-item  action-link">Profile <i class="pull-right fa fa-user"></i></a>
        </div>

        <!-- Create Modal -->
        <div class="modal fade" id="modal-add-sms" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <h4 class="modal-title">
                            Buy SMS
                        </h4>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-danger" v-if="createForm.errors.length > 0" >
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <p>
                                <strong>   {{ createForm.errors }} </strong>
                            </p>
                        </div>

                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Phone Number</label>

                                <div class="col-md-7">
                                    <input  v-validate="'required|numeric|min:12'" :class="{'input': true, 'is-danger': errors.has('phone') }"
                                           type="number" class="form-control" v-model="createForm.phone" placeholder="2567000000" name="phone">
                                    <small v-show="errors.has('phone')" class="help is-danger">{{ errors.first('phone') }}</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label"> No. of SMS </label>

                                <div class="col-md-7">
                                    <select required class="form-control" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('no_of_sms') }"
                                            v-model="createForm.no_of_sms" name="no_of_sms" >
                                        <option v-for="sms in sms_chart" v-bind:value="sms.id">{{sms.no_of_sms}}</option>
                                    </select>
                                    <small v-show="errors.has('no_of_sms')" class="help is-danger">{{ errors.first('no_of_sms') }}</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Amount</label>

                                <div class="col-md-7">
                                    <p :class="{ 'control': true }">
                                        <input class="form-control" v-validate="'required|numeric'" :class="{'input': true, 'is-danger': errors.has('amount') }"
                                               v-model="createForm.amount" disabled name="amount" type="number" >
                                    </p>
                                    <small v-show="errors.has('amount')" class="help is-danger">{{ errors.first('amount') }}</small>

                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        <button type="button" class="btn btn-primary disabled" v-if="createForm.phone === '' || createForm.phone.length < 6  ">
                            Buy
                        </button>
                        <button type="button" class="btn btn-primary" @click="store" v-else="">
                            Buy
                        </button>
                    </div>
                </div>
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
                notifications : [],
                success : [],
                sms_chart : [],
                sms : [],

                createForm: {
                    no_of_sms: 1,
                    phone: '',
                    amount: '',
                    errors:''
                }
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
                this.getSms()
                this.getNotifications()
                this.getSmsChart()

                $('#modal-add-sms').on('shown.bs.modal', () => {
                });
            },

            /**
             * Show the form for adding sms.
             */
            showSmsForm() {
                $('#modal-add-sms').modal('show');
            },

            /**
             * add a sms
             */
            store() {
                this.persistSms(
                    'post', '/client/buy-sms',
                    this.createForm, '#modal-add-sms'
                );
            },

            /**
             * Persist the item to storage using the given form.
             */
            persistSms(method, uri, form, modal) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.getSms();
                        form.no_of_sms = [];
                        form.phone = [];
                        $(modal).modal('hide');
                        this.success = response.data
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = error.response.data;
                        } else {
                            form.errors = error.response.data;
                        }
                    });
            },
            getSmsCost () {
                let vm = this.createForm
                axios.get('/client/get-sms-cost/'+ vm.no_of_sms)
                    .then(response => {
                        vm.amount = response.data
                    })
                    .catch(function (error) {
                        vm.amount = 0
                    })
            },
            getNotifications(){
                axios.get('/client/get-notifications')
                    .then(response => {
                        this.notifications = response.data;
                    });
            },
            getSms(){
                axios.get('/client/get-sms')
                    .then(response => {
                        this.sms = response.data;
                    });
            },
            getSmsChart(){
                axios.get('/client/get-sms-chart')
                    .then(response => {
                        this.sms_chart = response.data;
                    });
            },

            home(){
                window.location = '/client/';
            },

            myNotifications(){
                window.location = '/client/notifications';
            },
            myItems(){
                window.location = '/client/my-items';
            },

            profile(){
                window.location = '/client/profile';
            },

        },
        watch: {
            'createForm.no_of_sms': function (newQuestion) {
                this.getSmsCost()
            }
        },

    }
</script>
