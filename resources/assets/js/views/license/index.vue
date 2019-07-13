<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('license.license_verification')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('license.license_verification')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit" @keydown="licenseForm.errors.clear($event.target.name)">
                            <h4 class="card-title">{{trans('license.license_verification')}}</h4>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">{{trans('license.access_code')}}</label>
                                        <input class="form-control" type="text" value="" v-model="licenseForm.access_code" name="access_code" :placeholder="trans('license.access_code')">
                                        <show-error :form-name="licenseForm" prop-name="access_code"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">{{trans('license.envato_email')}}</label>
                                        <input class="form-control" type="text" value="" v-model="licenseForm.envato_email" name="envato_email" :placeholder="trans('license.envato_email')">
                                        <show-error :form-name="licenseForm" prop-name="envato_email"></show-error>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                            	<button type="submit" class="btn btn-info waves-effect waves-light m-t-10">{{trans('license.verify')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	export default {
		data() {
			return {
				licenseForm: new Form({
					access_code: '',
					envato_email: ''
				})
			}
		},
		mounted(){
			if(helper.getConfig('l')) {
				this.$router.push('/');
			}
		},
		methods: {
			submit() {
				this.licenseForm.post('/api/license')
					.then(response => {
						toastr.success(response.message);
						this.$router.push('/');
					})
					.catch(error => {
						helper.showErrorMsg(error);
					});
			}
		}
	}
</script>