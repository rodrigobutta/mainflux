<template>
    <div class="modal fade task-config" tabindex="-1" role="dialog" aria-labelledby="taskConfig" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="taskConfig">{{trans('task.configuration')}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submit" @keydown="taskConfigForm.errors.clear($event.target.name)">
                        <div class="form-group">
                            <label for="">{{trans('task.progress_type')}}</label>
                            <div class="radio radio-info">
                                <input type="radio" value="manual" id="progress_type_manual" v-model="taskConfigForm.progress_type" :checked="taskConfigForm.progress_type === 'manual'" name="progress_type">
                                <label for="progress_type_manual"> {{trans('task.manual_progress')}} </label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" value="sub_task" id="progress_type_sub_task" v-model="taskConfigForm.progress_type" :checked="taskConfigForm.progress_type === 'sub_task'" name="progress_type">
                                <label for="progress_type_sub_task"> {{trans('task.sub_task_progress')}} </label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" value="question" id="progress_type_question" v-model="taskConfigForm.progress_type" :checked="taskConfigForm.progress_type === 'question'" name="progress_type">
                                <label for="progress_type_question"> {{trans('task.question')}} </label>
                            </div>
                            <show-error :form-name="taskConfigForm" prop-name="progress_type"></show-error>
                        </div>
                        <div class="form-group">
                            <label for="">{{trans('task.rating_type')}}</label>
                            <div class="radio radio-info">
                                <input type="radio" value="task_based" id="rating_type_task_based" v-model="taskConfigForm.rating_type" :checked="taskConfigForm.rating_type === 'task_based'" name="rating_type">
                                <label for="rating_type_task_based"> {{trans('task.task_based_rating')}} </label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" value="sub_task_based" id="rating_type_sub_task_based" v-model="taskConfigForm.rating_type" :checked="taskConfigForm.rating_type === 'sub_task_based'" name="rating_type">
                                <label for="rating_type_sub_task_based"> {{trans('task.sub_task_based_rating')}} </label>
                            </div>
                            <show-error :form-name="taskConfigForm" prop-name="rating_type"></show-error>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light">{{trans('general.save')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        components:{},
        data() {
            return {
                taskConfigForm: new Form({
                    progress_type : 'manual',
                    rating_type: 'task_based'
                },false),
            }
        },
        props: ['uuid','task'],
        mounted() {
        },
        methods: {
            submit(){
                this.taskConfigForm.post('/api/task/'+this.uuid+'/config')
                    .then(response => {
                        toastr.success(response.message);
                        this.closeModal();
                        this.$emit('completed');

                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            closeModal(){
                $('.task-config').modal('hide');
            }
        },
        watch: {
            task(task){
                this.taskConfigForm.progress_type = task.progress_type;
                this.taskConfigForm.rating_type = task.rating_type;
            }
        }
    }
</script>
