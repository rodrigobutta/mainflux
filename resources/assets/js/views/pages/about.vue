<template>
	<div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('general.about')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('general.about')}}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="card">
                    <div class="card-body">
                    	<h4 class="card-title">{{trans('general.about')}}</h4>
                    	<div v-html="content"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="card">
                    <div class="card-body">
                    	<h4 class="card-title">{{trans('general.product_information')}}</h4>
                    	<product :product="product"></product>
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
				content: '',
				product: {}
			}
		},
		mounted(){
			if(!helper.getConfig('show_about_menu') || !helper.hasRole('admin')) {
				this.$router.push('/');
			}
            toastr.success(i18n.general.wait_while_loading);
			axios.get('/api/about')
				.then(response => response.data)
				.then(response => {
					this.content = response.about;
					this.product = response.product;
				})
				.catch(error => {
					helper.showDataErrorMsg(error);
				})
		},
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        }
	}
</script>