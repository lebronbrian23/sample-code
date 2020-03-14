<template>
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Register</div>
            <div class="panel-body">
                <form class="form-horizontal" method="POST" >

                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">Name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" name="name" v-model="Form.name"  required autofocus>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="phone" class="col-md-4 control-label">Mobile Phone</label>

                        <div class="col-md-6">
                            <input id="phone" type="number" class="form-control" name="mobile_no" v-model="Form.mobile_no"  required>
                            <small class="help is-danger">{{checking}}</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary disabled" v-if="availability === 'no'" >
                                Register
                            </button>
                            <button type="submit" class="btn btn-primary" @click="register" v-else>
                                Register
                            </button>
                        </div>
                    </div>
                </form>
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
                success : [],
                checking : '',
                availability : 'no',

                Form: {
                    mobile_no: '',
                    name: '',
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
        watch: {
            'Form.mobile_no': function (newQuestion) {
                this.checking = 'checking availability...';
                this.checkAvailability()

            }
        },
        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {

            },
            register(){
                let vm = this;
                axios.post('register' , vm.Form)
                    .then( function (response) {
                        console.log(response.data);
                        window.location = '/';
                    })
                    .catch( function (error) {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data));
                        } else {
                            form.errors = _.flatten(_.toArray(error.response.data));
                        }
                    })

            },


            registers(){
                this.persistRegister(
                    'post', 'register',
                    this.Form
                );
            },
            /**
             * Persist the user to storage using the given form.
             */
            persistRegister(method, uri, form) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        form.name = [];
                        form.mobile_no = [];
                        this.success = response.data
                       // window.location = '/'
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors =error.response.data;
                        } else {
                            form.errors = error.response.data;
                        }
                    });
            },
            checkAvailability: _.debounce(
                function () {
                    let vm = this.checkAvailability;

                        axios.get('/check-mobile-no/' + this.Form.mobile_no )
                            .then(response => {
                                console.log(response.data)
                                if(response.data === 'yes'){
                                    this.availability = 'yes'
                                    this.checking =''
                                } else {
                                    this.availability = 'no'
                                    this.checking ='mobile number is already used'
                                }
                            })
                            .catch(function (error) {
                                vm = 'mobile number is already used'
                            })

                },
                1000
            ),
        }
    }
</script>
