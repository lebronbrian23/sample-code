<template>
    <div>
        <div class="alert alert-danger" v-if="createForm.errors.length > 0" >
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            <p>
                <strong>   {{ createForm.errors }} </strong>
            </p>
        </div>
        <p>A code has been sent to this phone number <strong class="danger">+{{user.mobile_no}}</strong> </p>
        <p>Fot testing purpose this is the  code that was  sent  <strong class="danger">{{user.confirmation_code}}</strong> </p>
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label class="col-md-3 control-label">Confirmation Code</label>

                <div class="col-md-7">
                    <p :class="{ 'control': true }">
                        <input class="form-control" v-validate="'required|numeric|min:5|max:5'" :class="{'input': true, 'is-danger': errors.has('code') }"
                               v-model="createForm.code" name="code" type="number" >
                    </p>
                    <small v-show="errors.has('code')" class="help is-danger">{{ errors.first('code') }}</small>

                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-md-offset-5" >
                    <div class="col-md-6">
                        <button type="button" class="btn btn-primary" @click="store">
                            Verify
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a  @click="resendCode" class="action-link">Resend code</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                user : [],
                success : [],

                createForm: {
                    code: '',
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
                this.getUser()

            },


            /**
             * add a sms
             */
            store() {
                this.persistCode(
                    'post', '/client/confirm-phone',
                    this.createForm,
                );
            },

            /**
             * Persist the item to storage using the given form.
             */
            persistCode(method, uri, form) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.success = response.data
                        form = [];
                        window.location = '/client/'
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = error.response.data;
                        } else {
                            form.errors = error.response.data;
                        }
                    });
            },

            getUser(){
                axios.get('/client/get-user')
                    .then(response => {
                        this.user = response.data;
                    });
            },

            resendCode(){
                axios.get('/client/resend-code')
                    .then(response => {
                        window.location = '/client/'
                    });
            },
        }
    }
</script>
