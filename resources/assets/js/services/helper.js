import store from '../store'
import router from '../routes'

export default {
    // to logout user
    logout(){
        return axios.post('/api/auth/logout').then(response => response.data).then(response =>  {
            toastr.success(response.message);
        }).catch(error => {
            this.showDataErrorMsg(error);
        });
    },

    // to get authenticated user data
    authUser(){
        return axios.get('/api/auth/user').then(response => response.data).then(response =>  {
            return response;
        }).catch(error => {
            this.showDataErrorMsg(error);
        });
    },

    // to check for authenticated user
    check(){
        return axios.post('/api/auth/check').then(response => response.data).then(response =>  {

            store.dispatch('setConfig',response.config);

            if(response.failed_install){
                return false;
            } else
                store.dispatch('setConfig',{'failed_install':0});

            if(response.ip_restricted)
                localStorage.setItem('ip_restricted',true);
            else
                localStorage.removeItem('ip_restricted');

            if(response.authenticated){
                store.dispatch('setAuthUserDetail',{
                    id: response.user.id,
                    first_name: response.user.profile.first_name,
                    last_name: response.user.profile.last_name,
                    email: response.user.email,
                    avatar:response.user.profile.avatar,
                    company_name: response.company.name,                    
                    roles:response.user_roles
                });
                store.dispatch('setPermission',response.permissions);
                store.dispatch('setDefaultRole',response.default_role);

                this.setLastActivity();
            } else {
                store.dispatch('resetAuthUserDetail');
            }

            return response.authenticated;
        }).catch(error =>{
            store.dispatch('resetAuthUserDetail');
            store.dispatch('resetConfig');
        });
    },

    // to set notification position
    notification(){
        var notificationPosition = this.getConfig('notification_position') || 'toast-top-right';
        toastr.options = {
            "positionClass": notificationPosition
        };
        this.setLastActivity();

        $('[data-toastr]').on('click',function(){
            var type = $(this).data('toastr'),message = $(this).data('message'),title = $(this).data('title');
            toastr[type](message, title);
        });
    },

    setLastActivity(){
        if(!this.isScreenLocked())
            store.dispatch('setLastActivity')
    },

    // to check for last activity time and lock/unlock screen
    isScreenLocked(){
        let last_activity = this.getLastActivity();
        let lock_screen_timeout = this.getConfig('lock_screen_timeout');
        let last_activity_after_timeout = moment(last_activity).add(lock_screen_timeout,'minutes').format('LLL');
        return (moment().format('LLL') > last_activity_after_timeout);
    },

    // to append filter variables in the URL
    getFilterURL(data){
        let url = '';
        $.each(data, function(key,value) {
            url += (value) ? '&'+key+'='+encodeURI(value) : '';
        });
        return url;
    },

    getTwoFactorCode(){
        return store.getters.getTwoFactorCode;
    },

    getLastActivity(){
        return store.getters.getLastActivity;
    },

    // to get Auth Status
    isAuth(){
        return store.getters.getAuthStatus;
    },

    // to get Auth user detail
    getAuthUser(name){
        if(name === 'full_name')
            return store.getters.getAuthUser('first_name')+' '+store.getters.getAuthUser('last_name');
        else if(name === 'avatar'){
            if(store.getters.getAuthUser('avatar'))
                return '/'+store.getters.getAuthUser('avatar');
            else
                return '/images/avatar.png';
        }
        else
            return store.getters.getAuthUser(name);
    },

    // to get User avatar
    getAvatar(user){
        if(user && user.profile.avatar)
            return '/'+user.profile.avatar;
        else
            return '/images/avatar.png';
    },

    // to get config
    getConfig(config){
        return store.getters.getConfig(config);
    },

    // to get default role name of system
    getDefaultRole(role){
        return store.getters.getDefaultRole(role);
    },

    // to check role of authenticated user
    hasRole(role){
        return store.getters.hasRole(this.getDefaultRole(role));
    },

    // to check permission for authenticated user
    hasPermission(permission){
        return store.getters.hasPermission(permission);
    },

    // to check Admin role
    hasAdminRole(){
        if(this.hasRole('admin'))
            return 1;
        else
            return 0;
    },

    // to check whether a given user has given role
    userHasRole(user,role_name){
        if(!user.roles)
            return false;

        let user_role = user.roles.filter(role => role.name === this.getDefaultRole(role_name))
        if(user_role.length)
            return true;
        return false;
    },

    // to check feature is available or not
    featureAvailable(feature){
        return this.getConfig(feature);
    },

    // returns not accessible message if permission is denied
    notAccessibleMsg(){
        toastr.error(i18n.permission.permission_denied);
    },

    // returns feature not available message if permission is denied
    featureNotAvailableMsg(){
        toastr.error(i18n.general.feature_not_available);
    },

    // returns user status
    getUserStatus(user){
        let status = [];

        if(user.status === 'activated')
            status.push({'color': 'success','label': i18n.user.status_activated});
        else if(user.status === 'pending_activation')
            status.push({'color': 'warning','label': i18n.user.status_pending_activation});
        else if(user.status === 'pending_approval')
            status.push({'color': 'warning','label': i18n.user.status_pending_approval});
        else if(user.status === 'banned')
            status.push({'color': 'danger','label': i18n.user.status_banned});
        else if(user.status === 'disapproved')
            status.push({'color': 'danger','label': i18n.user.status_disapproved});

        return status;
    },

    getUserNameWithDesignationAndDepartment(user){
        if(!user.profile)
            return;

        if(user.profile.designation && user.profile.designation.department)
            return user.profile.first_name+' '+user.profile.last_name+' ('+user.profile.designation.name+' '+i18n.general.in+' '+user.profile.designation.department.name+')';
        else
            return user.profile.first_name+' '+user.profile.last_name;
    },

    // returns job number
    getJobNumber(job){
        return this.getConfig('job_number_prefix')+this.formatWithPadding(job.id,this.getConfig('job_number_digit'));
    },

    // returns job status
    getJobStatus(job){
        let status = [];

        if(!job.user.length)
            status.push({'color': 'danger','label': i18n.job.unassigned});

        if(job.sign_off_status === 'approved')
            status.push({'color': 'success','label': i18n.job.completed});
        else if(job.sign_off_status === 'requested')
            status.push({'color': 'warning','label': i18n.job.requested});
        else
            status.push({'color': 'info','label': i18n.job.pending});

        if(job.sign_off_status === 'rejected')
            status.push({'color': 'danger','label': i18n.job.rejected});

        if(job.sign_off_status !== 'approved' && moment(job.due_date).format('YYYY-MM-DD') < moment().format('YYYY-MM-DD')){
            let today = moment(new Date(),'YYYY-MM-DD');
            let due_date = moment(job.due_date,'YYYY-MM-DD');
            let overdue = today.diff(due_date,'days');
            status.push({'color': 'danger','label': i18n.job.overdue_by+' '+overdue+' '+i18n.job.day});
        } else if(job.sign_off_status === 'approved' && moment(job.completed_at).format('YYYY-MM-DD') > moment(job.due_date).format('YYYY-MM-DD')){
            let completed_at = moment(job.completed_at,'YYYY-MM-DD');
            let due_date = moment(job.due_date,'YYYY-MM-DD');
            let late = completed_at.diff(due_date,'days');
            status.push({'color': 'danger','label': i18n.job.late_by+' '+late+' '+i18n.job.day});
        }

        return status;
    },

    // returns user status
    getSubJobStatus(subJob){
        if(subJob.status)
            return {'color': 'success','label': i18n.job.complete};
        else
            return {'color': 'danger','label': i18n.job.incomeplete};
    },

    // returns job progress
    getJobProgress(job){
        let progress = 0
        if(job.progress_type === 'manual')
            progress = job.progress;
        else if (job.progress_type === 'question')
            progress = (job.answers && job.answers.length) ? 100 : 0;
        else {
            let completed_sub_job = 0;
            job.sub_job.forEach(function(element){
                completed_sub_job += (element.status) ? 1 : 0;
            });
            progress = (job.sub_job.length) ? this.formatNumber((completed_sub_job/job.sub_job.length)*100) : 0;
        }
        return progress;
    },

    // returns job progress color
    getJobProgressColor(job){
        let progress = this.getJobProgress(job);

        let classes = ['progress-bar','progress-bar-striped'];
        if(progress <= 25)
            classes.push('bg-danger');
        else if(progress <= 50)
            classes.push('bg-warning');
        else if(progress <= 80)
            classes.push('bg-info');
        else
            classes.push('bg-success');
        return classes;
    },

    // to mass assign one object in another object
    formAssign(form, data){
        for (let key of Object.keys(form)) {
            if(key !== "originalData" && key !== "errors" && key !== "autoReset" && key !== "providers"){
                form[key] = data[key] || '';
            }
        }
        return form;
    },

    // to get date in desired format
    formatDate(date){
        if(!date)
            return;

        return moment(date).format(this.getConfig('date_format'));
    },

    // to get date time in desired format
    formatDateTime(date){
        if(!date)
            return;

        var date_format = this.getConfig('date_format');
        var time_format = this.getConfig('time_format');

        return moment(date).format(date_format+' '+time_format);
    },

    // to get time from now
    formatDateTimeFromNow(datetime){
        if(!datetime)
            return;

        return moment(datetime).fromNow();
    },

    // to change first character of every word to upper case
    ucword(value){
        if(!value)
            return;

        return value.toLowerCase().replace(/\b[a-z]/g, function(value) {
            return value.toUpperCase();
        });
    },

    // to change string into human readable format
    toWord(value){
        if(!value)
            return;

        value = value.replace(/-/g, ' ');
        value = value.replace(/_/g, ' ');

        return value.toLowerCase().replace(/\b[a-z]/g, function(value) {
            return value.toUpperCase();
        });
    },

    // shows toastr notification for axios request
    showDataErrorMsg(error){
        this.setLastActivity();

        if(error.hasOwnProperty("error")){
            if (error.error.indexOf(' ') >= 0)
                toastr.error(error.error);
            else
                toastr.error(i18n.general[error.error]);

            if(error.error === 'token_expired')
                router.push('/login');
        } else if(error.hasOwnProperty("response") && error.response.status == 403) {
            toastr.error(i18n.general.permission_denied);
        } else if(error.hasOwnProperty("response") && error.response.status == 422 && error.response.data.hasOwnProperty("error")) {
            toastr.error(error.response.data.error);
        } else if(error.hasOwnProperty("response") && error.response.status == 404) {
            toastr.error(i18n.general.invalid_link);
        } else if(error.response.data.errors.hasOwnProperty("message"))
            toastr.error(error.response.data.errors.message[0]);
    },

    // returns error message for axios request
    fetchDataErrorMsg(error){
        return error.response.data.errors.message[0];
    },

    // shows toastr notification for axios form request
    showErrorMsg(error){
        this.setLastActivity();

        if(error.hasOwnProperty("error")){

            if (error.error.indexOf(' ') >= 0)
                toastr.error(error.error);
            else
                toastr.error(i18n.general[error.error]);

            if(error.error === 'token_expired')
                router.push('/login');
        } else if(error.hasOwnProperty("response") && error.response.status == 403) {
            toastr.error(i18n.general.permission_denied);
        } else if(error.hasOwnProperty("response") && error.response.status == 422 && error.response.data.hasOwnProperty("error")) {
            toastr.error(error.response.data.error);
        } else if(error.hasOwnProperty("response") && error.response.status == 404) {
            toastr.error(i18n.general.invalid_link);
        } else if(error.errors.hasOwnProperty("message"))
            toastr.error(error.errors.message[0]);
    },

    // returns error message for axios form request
    fetchErrorMsg(error){
        return error.errors.message[0];
    },

    // round numbers as given precision
    roundNumber(number, precision){
        precision = Math.abs(parseInt(precision)) || 0;
        var multiplier = Math.pow(10, precision);
        return (Math.round(number * multiplier) / multiplier);
    },

    // round numbers as given precision
    formatNumber(number,decimal_place){
        if (decimal_place === undefined)
            decimal_place = 2;
        return this.roundNumber(number,decimal_place);
    },

    // fill number with padding
    formatWithPadding(n, width, z){
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
    },

    // generates random string of certain length
    randomString(length) {
        if (length === undefined)
            length = 40;
        var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var result = '';
        for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
        return result;
    },

    // get job user rating
    getJobUserRating(user,job){
        if(job.sign_off_status !== 'approved')
            return;

        if(job.rating_type === 'job_based'){
            if(user.pivot.rating)
                return this.generateRatingStar(user.pivot.rating);
            return;
        } else if(job.rating_type === 'sub_job_based'){
            let rating = 0;
            let sub_job_count = 0;
            job.sub_job.forEach(function(sub_job){
                let sub_job_rating = sub_job.sub_job_rating.filter(sub_job_rating => sub_job_rating.user_id == user.id);
                if(sub_job_rating.length){
                    rating = rating + sub_job_rating[0].rating;
                    sub_job_count = sub_job_count + 1;
                }
            });
            let average_rating = (sub_job_count) ? rating/sub_job_count : 0;
            return this.generateRatingStar(average_rating);
        }
    },

    // generates rating star
    generateRatingStar(rating){
        let rating_fraction = rating.toFixed(2);
        rating = Math.floor(rating);
        let half_star_rating = rating_fraction - rating;
        let stars = '';
        for(let i = 1; i <= rating; i++)
            stars += '<i class="fa fa-star fa-lg starred"></i> ';

        if(half_star_rating)
            stars += '<i class="fa fa-star-half fa-lg starred"></i>';

        return stars;
    },

    bytesToSize(bytes) {
       var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
       if (bytes == 0) return '0 Byte';
       var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
       return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }
}
