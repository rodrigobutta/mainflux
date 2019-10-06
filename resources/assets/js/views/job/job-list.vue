<template>
    <div>
        <h6 class="card-subtitle" v-if="jobs">{{trans('general.total_result_found',{'count' : jobs.total})}}</h6>
        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
        <div class="table-responsive">
            <table class="table" v-if="jobs.total">
                <thead>
                    <tr>
                        <th>{{trans('job.number')}}</th>
                        <th>{{trans('job.title')}}</th>
                        <th>{{trans('job.category')}}</th>
                        <th>{{trans('job.priority')}}</th>
                        <th>{{trans('job.start_date')}}</th>
                        <th>{{trans('job.due_date')}}</th>
                        <th>{{trans('job.progress')}}</th>
                        <th>{{trans('job.status')}}</th>
                        <th>{{trans('job.owner')}}</th>
                        <th class="table-option">{{trans('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="job in jobs.data">
                        <td v-text="getJobNumber(job)"></td>
                        <td v-text="job.title"></td>
                        <td v-text="job.job_category.name"></td>
                        <td v-text="job.job_priority.name"></td>
                        <td>{{ job.start_date | moment}}</td>
                        <td>{{ job.due_date | moment}}</td>
                        <td>
                            <div class="progress" style="height: 10px;">
                                <div :class="getJobProgressColor(job)" role="progressbar" :style="getJobProgressWidth(job)" :aria-valuenow="getJobProgress(job)" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            {{ getJobProgress(job) }} %
                        </td>
                        <td>
                            <span v-for="status in getJobStatus(job)" :class="['label','label-'+status.color]" style="margin-right:5px;">{{status.label}}</span>
                        </td>
                        <td>{{ job.user_added.profile.first_name+' '+job.user_added.profile.last_name}}</td>
                        <td class="table-option">
                            <div class="btn-group">
                                <router-link :to="`/job/${job.uuid}`" class="btn btn-success btn-sm" v-tooltip="trans('job.view_job')"><i class="fas fa-arrow-circle-right"></i></router-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['option'],
        data(){
            return {
                jobs: []
            }
        },
        mounted(){
            this.getJobs(this.option);
        },
        methods: {
            getJobs(val){
                let url = '';
                if(val === 'starred')
                    url = '/api/job?page=1&page_length=5&sortBy=created_at&order=desc&starred=1';
                else if(val === 'owned')
                    url = '/api/job?page=1&page_length=5&sortBy=created_at&order=desc&type=owned';
                else if(val === 'pending' || val === 'unassigned' || val === 'overdue')
                    url = '/api/job?page=1&page_length=5&sortBy=created_at&order=desc&status='+val;
                else
                    url = '/api/job?page=1&page_length=5&sortBy=created_at&order=desc';
                axios.get(url)
                    .then(response => response.data)
                    .then(response => this.jobs = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getJobNumber(job){
                return helper.getJobNumber(job);
            },
            getJobProgressColor(job){
                return helper.getJobProgressColor(job);
            },
            getJobProgressWidth(job){
                return 'width: '+this.getJobProgress(job)+'%;';
            },
            getJobProgress(job){
                return helper.getJobProgress(job);
            },
            getJobStatus(job){
                return helper.getJobStatus(job);
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        }
    }
</script>
