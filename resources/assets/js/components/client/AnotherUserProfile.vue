<template>
    <div>

        <div class="panel-heading">{{Name}}'s Profile</div>

        <div class="panel-body panel ">
            <div class="row">
                <div class="col-md-3">
                    <div class="panel-heading">Photo</div>
                    <img class="img-responsive img-circle" :src="user.picture" :alt="user.name">
                </div>
                <div class="col-md-5">
                        <div class="form-group">
                            <a class="col-md-4 control-label">Name</a>

                            <div class="col-md-6">
                                {{ user.name}}
                            </div>
                        </div>
                        <div class="form-group">
                            <a class="col-md-4 control-label">Address</a>

                            <div class="col-md-6">
                                {{ user.address}}
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
        props:['userNo', 'Name'],

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getUser()

            },



            getUser(){
                axios.get('/client/get-user-profile/'+this.userNo)
                    .then(response => {
                        this.user = response.data;
                    });
            },
        }

    }
</script>
