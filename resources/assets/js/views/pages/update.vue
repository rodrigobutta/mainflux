<template>
	<div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('general.update')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('general.update')}}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="card">
                    <div class="card-body">
                    	<h4 class="card-title">{{trans('general.update')}}</h4>
                        <div v-if="product.current_version == product.latest_version && product.name" class="alert alert-danger">No update available! Please check later.</div>
                        <div v-else>
                            <template v-if="!is_processing">
                                <div v-html="update_tips"></div>
                                <div class="table-responsive" v-if="product.name">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th>Version Available for Upgrade</th>
                                                <td>{{product.next_release_version}}</td>
                                            </tr>
                                            <tr>
                                                <th>Date of Release</th>
                                                <td>{{product.next_release_date | moment}}</td>
                                            </tr>
                                            <tr>
                                                <th>Update Size</th>
                                                <td>{{getFileSize(product.next_release_size)}}</td>
                                            </tr>
                                            <tr>
                                                <th colspan="2" v-html="product.next_release_change_log"></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-info" key="download" v-confirm="{ok: confirmDownload(0)}" v-if="!is_downloaded">Download</button>
                                    <button type="button" class="btn btn-info" key="direct-update" v-confirm="{ok: confirmUpdate()}" v-if="is_downloaded">Update</button>
                                    <button type="button" class="btn btn-success" key="download-update" v-confirm="{ok: confirmDownload(1)}" v-if="!is_downloaded">Download & Update</button>
                                </div>
                            </template>
                            <template v-else>
                                <p class="text-center">Don't perform any action till we are performing update!</p>
                                <p class="text-center" v-if="is_downloading">Update Size ({{getFileSize(product.next_release_size)}}) - Downloading.....</p>
                                <p class="text-center" v-if="is_updating">Updating.....</p>
                                <button type="button" class="btn btn-info" key="update" v-confirm="{ok: confirmUpdate()}" v-if="is_downloaded">Update</button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="card">
                    <div class="card-body">
                    	<h4 class="card-title">{{trans('general.product_information')}}</h4>
                    	<product :product="product" update="1"></product>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import product from './product'

	export default {
		components: {product},
		data() {
			return {
                update_tips: '',
				product: {},
                is_processing: 0,
                is_downloading: 0,
                is_updating: 0,
                is_downloaded: 0,
                updateForm: new Form({
                    build: '',
                    version: ''
                })
			}
		},
		mounted(){
			if(!helper.hasRole('admin')) {
				this.$router.push('/');
			}

            toastr.success(i18n.general.wait_while_loading);
			axios.get('/api/update')
				.then(response => response.data)
				.then(response => {
                    this.update_tips = response.update_tips;
					this.product = response.product;

                    if (response.is_downloaded) {
                        this.is_downloaded = 1;
                        this.updateForm.build = this.product.next_release_build;
                        this.updateForm.version = this.product.next_release_version;
                    }
				})
				.catch(error => {
					helper.showDataErrorMsg(error);
				})
		},
		methods: {
            confirmUpdate(){
                return dialog => this.update();
            },
            confirmDownload(action){
                return dialog => this.download(action);
            },
			download(action){
                this.is_processing = 1;
                this.is_downloading = 1;
                axios.post('/api/download')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.is_downloading = 0;
                        this.is_downloaded = 1;
                        this.updateForm.build = response.release.build;
                        this.updateForm.version = response.release.version;

                        if(action)
                            this.update();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.is_processing = 0;
                        this.is_downloading = 0;
                    })
			},
            update(){
                this.is_updating = 1;
                this.updateForm.post('/api/update')
                    .then(response => {
                        toastr.success(response.message);
                        location.reload();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                        this.is_processing = 0;
                        this.is_updating = 0;
                    })
            },
            getFileSize(size){
                return helper.bytesToSize(size);
            }
		},
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        },
        computed: {
            checkSupportValidity(){
                if (moment().format('YYYY-MM-DD') <= this.product.date_of_support_expiry)
                    return true;
                else
                    return false;
            }
        }
	}
</script>