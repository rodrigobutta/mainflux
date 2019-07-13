<template>
	<div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('general.support')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('general.support')}}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="card">
                    <div class="card-body">
                    	<h4 class="card-title">{{trans('general.support')}}</h4>
                    	<div class="alert alert-danger" v-if="product.name && !checkSupportValidity">Your support is expired. Please renew your support.</div>
                    	<div v-else>
                            <div v-html="support_tips"></div>
	                        <form @submit.prevent="submit" @keydown="supportForm.errors.clear($event.target.name)">
                                <div class="form-group">
                                    <input class="form-control" type="text" value="" v-model="supportForm.subject" name="subject" placeholder="Subject">
                                    <show-error :form-name="supportForm" prop-name="subject"></show-error>
                                </div>
                                <div class="form-group">
                                    <autosize-textarea rows="5" class="form-control" v-model="supportForm.body" placeholder="Body" name="body"></autosize-textarea>
                                    <show-error :form-name="supportForm" prop-name="body"></show-error>
                                </div>
                              	<div class="form-group">
                              		<button type="submit" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                              	</div>
                            </form>
                    	</div>
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
    import autosizeTextarea from '../../components/autosize-textarea'

	export default {
		components: {product,autosizeTextarea},
		data() {
			return {
                support_tips: '',
				product: {},
				supportForm: new Form({
					subject: '',
					body: '',
					purchase_code: '',
					product_name: '',
					date_of_support_expiry: ''
				})
			}
		},
		mounted(){
			if(!helper.getConfig('show_support_menu') || !helper.hasRole('admin')) {
				this.$router.push('/');
			}

            toastr.success(i18n.general.wait_while_loading);
			axios.get('/api/support')
				.then(response => response.data)
				.then(response => {
                    this.support_tips = response.support_tips;
					this.product = response.product;
					this.supportForm.purchase_code = this.product.purchase_code;
					this.supportForm.product_name = this.product.name;
					this.supportForm.date_of_support_expiry = this.product.date_of_support_expiry;
				})
				.catch(error => {
					helper.showDataErrorMsg(error);
				})
		},
		methods: {
			submit(){
				this.supportForm.post('/api/support')
					.then(response => {
						toastr.success(response.message);
					})
					.catch(error => {
						helper.showErrorMsg(error);
					});
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