<template>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fas fa-envelope"></i>
            <div class="notify"  v-if="unreadMessages.length > 0">
                <span class="badge badge-pill badge-warning">{{ unreadMessages.length }}</span>
            </div>
        </a>        
        <div :class="['dropdown-menu messages animated faster fadeInDown ', getConfig('direction') != 'rtl' ? 'dropdown-menu-right' : '']">
            <ul>
                <li>
                    <div class="drop-title">Messages</div>
                </li>
                <li>
                    <div class="message">

                        <a v-for="item in messages" :key="item.id" v-on:click="messageOpen(item, $event)" v-bind:class="{ read: item.read }">
                            <div class="btn btn-danger btn-circle"><i class="fa fa-link"></i></div>
                            <div class="body">
                                <h5>{{ item.subject }}<sup>({{ item.count }})</sup></h5> <span class="mail-desc">{{ item.user_from.email }}</span> <span class="time">9:30 AM</span>
                            </div>
                            <!-- <button type="button" v-on:click="messageDismiss(item, $event)" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button> -->
                        </a>

                    </div>
                </li>
                <li>
                    <a class="nav-link text-center" href="javascript:void(0);"> <strong>Check all messages</strong> <i class="fa fa-angle-right"></i> </a>
                </li>
            </ul>
        </div>
    </li>

</template>

<script>
    export default {
        data(){
            return {
                messages: [],
                checkMessagesInterval: null
            }
        },
        mounted() {
            this.getMessages();
            this.initCheckMessages()
        },
        methods : {    
           
            initCheckMessages () {
                this.checkMessagesInterval = setInterval(() => {
                    this.getMessages()
                }, 120000)
            },
            
            getMessages(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterMessageForm);
                axios.get('/api/message/inbox?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.messages = response.messages.data;                        
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getConfig(name){
                return helper.getConfig(name);
            },
            messageOpen(message,event){
                if (event) event.preventDefault()
                message.read=true;
                this.$router.push('/message/' + message.uuid); // TODO ojo que no funciona si ya estoy en algun mensaje y me quiero mover a otro mensaje
            },
            messageDismiss(message,event){
                if (event) event.preventDefault()

                // this.messages.splice(this.messages.indexOf(message), 1);
                
                // axios.get(this.getMessageApiUri(message,'dismiss'))
                // .then(response => response.data)
                // .then(response => {                                    
                // })
                // .catch(error => {
                //     helper.showDataErrorMsg(error);
                // });

            },            
        },
        beforeDestroy () {
            clearInterval(this.checkMessagesInterval)
        },
        computed: {
            unreadMessages(){
                return this.messages.filter(item => item.read === false);
            }
        }
    }
</script>
