<template>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-bell"></i>
            <div class="notify"  v-if="unreadNotifications.length > 0">
                <span class="heartbit"></span> <span class="badge badge-pill badge-danger">{{ unreadNotifications.length }}</span>
            </div>
        </a>        
        <div :class="['dropdown-menu mailbox animated faster fadeInDown ', getConfig('direction') != 'rtl' ? 'dropdown-menu-right' : '']">
            <ul>
                <li>
                    <div class="drop-title">Notifications</div>
                </li>
                <li>
                    <div class="message-center">

                        <a v-for="item in notifications" :key="item.id" v-on:click="notificationOpen(item, $event)" v-bind:class="{ read: item.read }">
                            <div class="btn btn-danger btn-circle"><i class="fa fa-link"></i></div>
                            <div class="mail-contnet">
                                <h5>{{ item.user }}</h5> <span class="mail-desc">{{ item.text }}</span> <span class="time">{{ item.unid }} 9:30 AM</span>
                            </div>
                            <button type="button" v-on:click="notificationDismiss(item, $event)" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        </a>

                    </div>
                </li>
                <li>
                    <a class="nav-link text-center" href="javascript:void(0);"> <strong>Check all notifications</strong> <i class="fa fa-angle-right"></i> </a>
                </li>
            </ul>
        </div>
    </li>

</template>

<script>
    export default {
        data(){
            return {
                notifications: []
            }
        },

        mounted() {

            Echo.channel('notifications')
                .listen('.newNotification', (notification) => {
                    notification.origin='push'; 
                    notification.read=false;                     
                    this.notifications.push(notification);
                });

            axios.get('/api/notification/fetch/inbox')
                .then(response => response.data)
                .then(response => {
                    console.log(response)                    
                    this.notifications = response.notifications;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

        },
        methods : {            
            notificationOpen(notification,event){
                if (event) event.preventDefault()

                notification.read=true;

                axios.get(this.getNotificationApiUri(notification,'read'))
                .then(response => response.data)
                .then(response => {                                    
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

                // ir al destino (EN PARALELO)
                if(notification.linkTarget=='_self'){
                    // debería poner timer para dejar tiempo al markread, o en callback pero no quiero que pueda colgar
                    this.$router.push(notification.link)
                }
                else{                    
                    window.open(notification.link, "_blank");   
                }

            },
            notificationDismiss(notification,event){
                if (event) event.preventDefault()

                this.notifications.splice(this.notifications.indexOf(notification), 1);
                
                axios.get(this.getNotificationApiUri(notification,'dismiss'))
                .then(response => response.data)
                .then(response => {                                    
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

            },
            getNotificationApiUri(notification,trail){
                
                if(notification.origin=='push'){
                    return '/api/notification/' + notification.origin + '/' + notification.unid + '/' + trail;  
                }
                else{                 
                    return '/api/notification/' + notification.origin + '/' + notification.id + '/' + trail;  
                }

            },
            getConfig(name){
                return helper.getConfig(name);
            }
        },
        computed: {
            unreadNotifications(){
                return this.notifications.filter(item => item.read === false);
            }
        }
    }
</script>
