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
                <strong class="m-b-none text-center" v-if="items.length === 0">
                    No items found.
                </strong>
                <masonry
                        :cols="3"
                        :gutter="20"
                >
                    <div v-for="item in items" >
                        <div class="card-deck padding-5px">
                            <div class="card">
                                <div>
                                    <a class="action-link" v-on:click="view(item)">
                                        <img class="card-img-top" :src="item.picture" :alt="item.name">
                                    </a>
                                </div>
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <strong><a class="action-link" v-on:click="view(item)"> {{item.name}}</a></strong>
                                        <small v-if="item.user_id === item.loggedUser_id" class="card-text pull-right">
                                            <a class="action-link" v-tooltip="'edit item'" v-on:click="edit(item)"><i class="fa fa-edit"></i></a>
                                            <a class="action-link danger" v-tooltip="'delete item'" v-on:click="revoke(item)"><i class="fa fa-trash"></i></a>
                                        </small>
                                    </h4>
                                    <p class="profile-card-bio">{{item.description}} </p>
                                    <small class="card-text"><i class="fa fa-money"></i>  <a class="action-link">{{item.price}}</a>
                                        ,<i v-if="item.type === 'Wanted'"><i class="fa fa-map-marker"></i> Delivery <span>{{item.location}} </span> </i>
                                        <i v-else><i class="fa fa-map-marker"></i> Located <span>{{item.location}} </span> </i>
                                    </small>
                                    <p><small>By <a class="action-link" @click="showSeller(item)">{{item.seller}}</a></small></p>
                                </div>
                                <div class="card-footer">
                                    <small class="muted">
                                        <span v-if="item.type === 'Wanted'"><strong><a class="action-link" v-tooltip="'item is wanted'">{{item.type}}</a></strong></span>
                                        <span v-else><strong><a class="action-link" v-tooltip="'item is being sold'">{{item.type}}</a></strong></span>
                                        <span class="pull-right">Posted {{item.created_at.date | humanReadableTime}}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </masonry>
                <ul class="pager" v-if="items.length > 0">
                    <li class="previous" :class="{ disabled: lacksOffset }"><a href="#" @click="getPrevious">&larr; Previous</a></li>
                    <li class="next" :class="{ disabled: lacksEntries }"><a href="#" @click="getNext">Next &rarr;</a></li>
                </ul>
            </div>
        </div>
        <!-- Create item Modal -->
        <div class="modal fade" id="modal-create-item" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <h4 class="modal-title">
                            Post Item
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
                                <label class="col-md-3 control-label">Name</label>

                                <div class="col-md-7">
                                    <input id="create-item" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('name') }"
                                          name="name" type="text" class="form-control" v-model="createForm.name">
                                    <small v-show="errors.has('name')" class="help is-danger">{{ errors.first('name') }}</small>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Description</label>

                                <div class="col-md-7">
                                    <textarea type="text" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('description') }"
                                              class="form-control" name="description" v-model="createForm.description"></textarea>
                                    <small v-show="errors.has('description')" class="help is-danger">{{ errors.first('description') }}</small>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Amount</label>

                                <div class="col-md-7">
                                    <p :class="{ 'control': true }">
                                        <input class="form-control" v-validate="'required|numeric'" :class="{'input': true, 'is-danger': errors.has('price') }"
                                               v-model="createForm.price" name="price" type="number" placeholder="2000" >
                                    </p>
                                    <small v-show="errors.has('price')" class="help is-danger">{{ errors.first('price') }}</small>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Location / Place of Delivery</label>

                                <div class="col-md-7">
                                    <p :class="{ 'control': true }">
                                        <input class="form-control" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('location') }"
                                               v-model="createForm.location" name="location" type="text" >
                                    </p>
                                    <small v-show="errors.has('location')" class="help is-danger">{{ errors.first('location') }}</small>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Type</label>

                                <div class="col-md-7">
                                    <p :class="{ 'control': true }">
                                        <select class="form-control" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('type') }"
                                                v-model="createForm.type" name="type" >
                                            <option value="selling">Selling</option>
                                            <option value="wanted">Wanted</option>
                                        </select>
                                    </p>
                                    <small v-show="errors.has('type')" class="help is-danger">{{ errors.first('type') }}</small>
                                </div>
                            </div>
                            <div class="form-group" v-if="!createForm.picture">
                                <label class="col-md-3 control-label">Picture </label>
                                <div class="col-md-7">
                                    <input type="file" @change="onFileChange" v-validate="'required|size:5250|mimes:image/jpeg,image/png|ext:jpg,png'" :class="{'input': true, 'is-danger': errors.has('picture') }">
                                    <small v-show="errors.has('picture')" class="help is-danger">{{ errors.first('picture') }}</small>
                                </div>
                            </div>
                            <div class="form-group" v-else>
                                <div class="col-md-4"></div>
                                <div class="col-md-6"><img :src="createForm.picture" /></div>
                                <div class="col-md-2"><button @click="removeImage" @keyup.prevent="store">Remove </button></div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        <i v-show="loading"  style="font-size: 15px; color:#663366;" class="fa fa-circle-o-notch fa-pulse fa-3x fa-fw"></i>
                        <span class="sr-only">posting...</span>
                        <button type="button" class="btn btn-primary disabled" v-if="createForm.name === '' || createForm.description === ''|| createForm.phone === '' || createForm.amount === '' || createForm.location === '' || createForm.type === '' || createForm.picture === ''  ">
                            Post
                        </button>
                        <button type="button" id="posting" class="btn btn-primary" @click="store" v-else="">
                            Post
                        </button>

                    </div>
                </div>
            </div>
        </div>

        <!-- Edit item Modal -->
        <div class="modal fade" id="modal-edit-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <h4 class="modal-title">
                            Edit Item
                        </h4>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-danger" v-if="editForm.errors.length > 0" >
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <p>
                                <strong>   {{ editForm.errors }} </strong>
                            </p>
                        </div>

                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Name</label>

                                <div class="col-md-7">
                                    <input id="create-client-name" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('name') }"
                                          name="name" type="text" class="form-control" v-model="editForm.name">
                                    <small v-show="errors.has('name')" class="help is-danger">{{ errors.first('name') }}</small>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Description</label>

                                <div class="col-md-7">
                                    <textarea type="text" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('description') }"
                                              class="form-control" name="description" v-model="editForm.description"></textarea>
                                    <small v-show="errors.has('description')" class="help is-danger">{{ errors.first('description') }}</small>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Amount</label>

                                <div class="col-md-7">
                                    <p :class="{ 'control': true }">
                                        <input class="form-control" v-validate="'required|numeric'" :class="{'input': true, 'is-danger': errors.has('price') }"
                                               v-model="editForm.price" name="price" type="number"  >
                                    </p>
                                    <small v-show="errors.has('price')" class="help is-danger">{{ errors.first('price') }}</small>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Location / Place of Delivery</label>

                                <div class="col-md-7">
                                    <p :class="{ 'control': true }">
                                        <input class="form-control" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('location') }"
                                               v-model="editForm.location" name="location" type="text" >
                                    </p>
                                    <small v-show="errors.has('location')" class="help is-danger">{{ errors.first('location') }}</small>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Type</label>

                                <div class="col-md-7">
                                    <p :class="{ 'control': true }">
                                        <select class="form-control" v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('type') }"
                                                v-model="editForm.type" name="type" >
                                            <option value="selling">Selling</option>
                                            <option value="wanted">Wanted</option>
                                        </select>
                                    </p>
                                    <small v-show="errors.has('type')" class="help is-danger">{{ errors.first('type') }}</small>
                                </div>
                            </div>
                            <div class="form-group" v-if="!editForm.picture">
                                <label class="col-md-3 control-label">Picture </label>
                                <div class="col-md-7">
                                    <input type="file" @change="onFileChange" v-validate="'required|size:5250|mimes:image/jpeg,image/png|ext:jpg,png'" :class="{'input': true, 'is-danger': errors.has('picture') }">
                                    <small v-show="errors.has('picture')" class="help is-danger">{{ errors.first('picture') }}</small>
                                </div>
                            </div>
                            <div class="form-group" v-else>
                                <div class="col-md-4"></div>
                                <div class="col-md-6"><img :src="editForm.picture" /></div>
                                <div class="col-md-2"><button @click="removeImage" @keyup.prevent="store">Remove </button></div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <i v-show="loadin"  style="font-size: 15px; color:#663366;" class="fa fa-circle-o-notch fa-pulse fa-3x fa-fw"></i>
                        <span class="sr-only">editing...</span>
                        <button type="button" class="btn btn-primary" @click="update">
                            Update
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
                loadin:false,
                loading:false,
                deleted: [],
                success: [],
                items: [],
                search: '',
                offset: 0,
                sms: '',
                search_param: '',

                createForm: {
                    errors: [],
                    name: '',
                    price: '',
                    location: '',
                    type: '',
                    description: '',
                    picture: '',

                },

                editForm: {
                    errors: [],
                    name: '',
                    price: '',
                    location: '',
                    type: '',
                    description: '',
                    picture: '',

                }
            };
        },

    computed:  {
        lacksEntries: function() {
            return this.items.length === 0
        },
        lacksOffset: function() {
            return this.offset === 0
        },
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
            search: function(newValue) {
                this.offset = 0;
                try {
                    this.getItems(this.getOptions())
                }catch(e) {
                    console.log(e)
                }
            }
        },
        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getSms()
                this.getItems();

                $('#modal-create-item').on('shown.bs.modal', () => {
                    $('#create-item').focus();
                });

                $('#modal-edit-item').on('shown.bs.modal', () => {
                    $('#edit-item').focus();
                });
            },
            getSms(){
                axios.get('/client/get-sms')
                    .then(response => {
                        this.sms = response.data;
                    });
            },
            /**
             * Get all of the items.
             */
            getItems(options = {offset: 0}) {
                axios.get('/client/get-items/search?' + this.urlEncodeOptions(options))
                    .then(response => {
                        this.items = response.data;
                    });
            },

            /**
             * Show the form for creating new item.
             */
            showCreateForm() {
                $('#modal-create-item').modal('show');
            },

            /**
             * Create a new item.
             */
            store() {
                this.persistItem(
                    'post', '/client/add-item',
                    this.createForm, '#modal-create-item' ,'loading'
                );
            },

            /**
             * Edit the given item.
             */
            edit(item) {
                this.editForm.id = item.id;
                this.editForm.name = item.name;
                this.editForm.price = item.price_2;
                this.editForm.picture = item.picture;
                this.editForm.location = item.location;
                this.editForm.description = item.description;
                this.editForm.type = item.type_2;

                $('#modal-edit-client').modal('show');
            },

            /**
             * Update the client being edited.
             */
            update() {
                this.persistItem(
                    'put', '/client/update-item/' + this.editForm.id,
                    this.editForm, '#modal-edit-client' ,'loadin'
                );
            },

            /**
             * Persist the item to storage using the given form.
             */
            persistItem(method, uri, form, modal , loader) {
                form.errors = [];
                loader = true
                $(' #posting ,#editing').addClass('disabled');

                axios[method](uri, form)
                    .then(response => {
                        this.getItems();
                        form = [];
                        loader = false
                        $(' #posting ,#editing').removeClass('disabled');
                        $(modal).modal('hide');
                        this.success = response.data
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            showSeller(seller){
                window.location = '/client/view-profile/'+ seller.seller_no +'/'+ seller.seller
            },

            /**
             * delete the given item.
             */
            revoke(item){
                this.$dialog.confirm('Are you sure ', { loader: true })
                    .then((dialog) => {
                        setTimeout(() => {
                            axios.post('/client/delete-item/' + item.id )
                                .then(response => {
                                    this.deleted = response.data;
                                    this.getItems();
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


            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let image = new Image();
                let reader = new FileReader();
                let vm = this.createForm;

                reader.onload = (e) => {
                    vm.picture = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            removeImage: function (e) {
                this.createForm.picture = '';
            },
            /**
             * go to next page.
             */
            getNext() {
                if (this.items.length){
                    this.offset += 10;
                    try {
                        this.getInvoices(this.getOptions())
                    }catch(e) {
                        console.log(e)
                    }
                }
            },
            /**
             * go to previous page.
             */
            getPrevious() {
                if (this.offset && (this.offset - 10) >= 0){
                    this.offset -= 10;
                    try {
                        this.getItems(this.getOptions())
                    }catch(e) {
                        console.log(e)
                    }
                }
            },
            /**
             * get the entry option .
             */
            getOptions() {
                let options = {offset: 0};
                if (this.offset){
                    options["offset"] = this.offset
                }
                if (this.search){
                    options["search"] = this.search
                }

                return options
            },
            /**
             * encode options into url .
             */
            urlEncodeOptions(options) {
                let uri = '';
                for(let i in options) {
                    uri += i + '=' + options[i] + '&'
                }
                return uri
            },

        }
    }
</script>
