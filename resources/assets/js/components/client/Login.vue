<template>
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Login</div>
            <div class="panel-body">
                <form class="form-horizontal" method="POST" >

                    <div class="form-group">
                        <label for="phone" class="col-md-4 control-label">Mobile Phone</label>

                        <div class="col-md-6">
                            <input id="phone" type="number" class="form-control" name="mobile_no" v-model="Form.mobile_no"  required>
                            <small class="help is-danger">{{checking}}</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary" @click="login" >
                                Login
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

                Form: {
                    mobile_no: '',
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

            },
            login(){
                let vm = this;
                axios.post('login' , vm.Form)
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

        }
    }
</script>
