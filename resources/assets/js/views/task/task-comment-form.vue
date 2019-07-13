<template>
    <div class="row">
        <div class="col-12">
            <form @submit.prevent="submit" @keydown="taskCommentForm.errors.clear($event.target.name)" class="">
                <div class="form-group">
                    <input class="form-control" type="text" value="" v-model="taskCommentForm.comment" name="comment" :placeholder="(type === 'reply') ? trans('task.type_comment',{type:trans('task.reply')}) : trans('task.type_comment',{type:trans('task.comment')})">
                    <show-error :form-name="taskCommentForm" prop-name="comment"></show-error>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        components: {},
        data(){
            return {
                taskCommentForm: new Form({
                    comment: '',
                    comment_id: null
                })
            }
        },
        props: ['uuid','id','type'],
        mounted(){
        },
        methods: {
            submit(){
                this.taskCommentForm.comment_id = this.id;
                this.taskCommentForm.post('/api/task/'+this.uuid+'/comment')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
