<template>
    <div class="row">
        <div class="col-12">
            <form @submit.prevent="submit" @keydown="jobCommentForm.errors.clear($event.target.name)" class="">
                <div class="form-group">
                    <input class="form-control" type="text" value="" v-model="jobCommentForm.comment" name="comment" :placeholder="(type === 'reply') ? trans('job.type_comment',{type:trans('job.reply')}) : trans('job.type_comment',{type:trans('job.comment')})">
                    <show-error :form-name="jobCommentForm" prop-name="comment"></show-error>
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
                jobCommentForm: new Form({
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
                this.jobCommentForm.comment_id = this.id;
                this.jobCommentForm.post('/api/job/'+this.uuid+'/comment')
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
