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
        <div class="alert alert-danger" v-if="deleted.length > 0" >
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            <p>
                <strong>   {{ deleted}} </strong>
            </p>
        </div>
        <div class="panel-heading">Profile</div>

        <div class="panel-body panel ">
            <div class="row">
                <div class="col-md-3">
                    <div class="panel-heading">Photo</div>
                    <img class="img-responsive img-circle" :src="user.picture" :alt="user.name">
                </div>
                <div class="col-md-5">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <a  class="col-md-4 control-label"> Name</a>

                            <div class="col-md-6">
                                <input type="text" class="form-control" v-model="form.name">
                            </div>
                        </div>
                        <div class="form-group">
                            <a class="col-md-4 control-label">Address </a>

                            <div class="col-md-6">
                                <input type="text" class="form-control" v-model="form.address" >
                            </div>
                        </div>
                        <div class="form-group">
                            <a class="col-md-4 control-label">Picture </a>

                            <div class="col-md-6">
                                <input type="file" @change="onFileChange" v-validate="'size:5250|mimes:image/jpeg,image/png|ext:jpg,png'" :class="{'input': true, 'is-danger': errors.has('picture') }">
                                <small v-show="errors.has('picture')" class="help is-danger">{{ errors.first('picture') }}</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <a class="col-md-4 control-label">Phone Number</a>

                            <div class="col-md-6">
                                {{ user.mobile_no}}
                            </div>
                        </div>
                        <div class="form-group">
                            <a class="col-md-4 control-label">Joined</a>

                            <div class="col-md-6">
                                {{ user.created_at | humanReadableTime }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-8">
                                <button type="button" class="btn btn-primary" @click="save">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
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
                user : [],
                deleted : [],
                success : [],

                form: {
                    name: '',
                    picture: '',
                    address: '',
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
             * method for updating a company.
             */
            save() {
                this.persistClient(
                    'put', '/client/update-info',
                    this.form
                );
            },

            /**
             * method to push to server  .
             */
            persistClient(method, uri, form ) {
                form.errors = [];
                axios[method](uri, form)
                    .then(response => {
                        this.getUser();
                        form.errors = [];
                        this.success =  response.data;
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
                        this.form.name = response.data.name;
                        this.form.address = response.data.address;
                        this.form.picture = response.data.picture;
                    });
            },
            editPicture(){

            },

            resendCode(){
                axios.get('/client/resend-code')
                    .then(response => {
                        window.location = '/client/'
                    });
            },
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let image = new Image();
                let reader = new FileReader();
                let vm = this.form;

                reader.onload = (e) => {
                    vm.picture = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            removeImage: function (e) {
                this.form.picture = '';
            },
        }

    }
</script>
