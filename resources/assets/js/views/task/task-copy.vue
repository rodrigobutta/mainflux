<template>
    <div class="card" v-if="showPanel">
        <div class="card-body">
            <h4 class="card-title">{{trans('task.task_copy')}}</h4>
            <div class="row">
                <div class="col-12">
                    <form @submit.prevent="copy" @keydown="taskCopyForm.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="assigned_user" type="checkbox" value="1" v-model="taskCopyForm.assigned_user">
                                        <label for="assigned_user"> {{trans('task.copy_assigned_user')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="sub_task" type="checkbox" value="1" v-model="taskCopyForm.sub_task">
                                        <label for="sub_task"> {{trans('task.copy_sub_task')}} </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="task_configuration" type="checkbox" value="1" v-model="taskCopyForm.task_configuration">
                                        <label for="task_configuration"> {{trans('task.copy_task_configuration')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="attachments" type="checkbox" value="1" v-model="taskCopyForm.attachments">
                                        <label for="attachments"> {{trans('task.copy_attachments')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="notes" type="checkbox" value="1" v-model="taskCopyForm.notes">
                                        <label for="notes"> {{trans('task.copy_notes')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input id="zero_progress" type="checkbox" value="1" v-model="taskCopyForm.zero_progress">
                                        <label for="zero_progress"> {{trans('task.set_zero_progress')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light">{{trans('task.copy')}}</button>
                        <button type="button" class="btn btn-danger" v-if="showPanel" @click="toggle">{{trans('general.cancel')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        components: {},
        props: {
            uuid:{
                required: true,
            },
            showPanel:{
                required: true,
            }
        },
        data(){
            return {
                taskCopyForm: new Form({
                    assigned_user: false,
                    sub_task: false,
                    task_configuration: false,
                    attachments: false,
                    notes: false,
                    zero_progress: false,
                })
            }
        },
        watch: {
        },
        mounted(){
        },
        methods: {
            copy(){
                this.taskCopyForm.post('/api/task/'+this.uuid+'/copy')
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/task/'+response.new_task.uuid);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });

            },
            toggle(){
                this.$emit('toggle');
            }
        },
        filters: {
        },
        computed: {

        }
    }
</script>
