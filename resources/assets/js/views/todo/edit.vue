<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('todo.edit_todo')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('todo.edit_todo')}}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('todo.edit_todo')}}</h4>
                        <todo-form :id="id"></todo-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import todoForm from './form';

    export default {
        components : { todoForm },
        data() {
            return {
                id:this.$route.params.id
            }
        },
        mounted(){
            if(!helper.hasPermission('access-todo')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('todo')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }
        }
    }
</script>
