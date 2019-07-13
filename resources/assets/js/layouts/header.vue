<template>
    <header class="topbar">
        <nav class="navbar top-navbar navbar-expand-md navbar-light">
            <div class="navbar-header">
                <router-link class="navbar-brand" to="/">
                    <b>
                        <img :src="getSidebarLogo" alt="" class="dark-logo" />
                        <img :src="getSidebarLogo" alt="" class="light-logo" />
                    </b>
                    <span>
                     <img :src="getMainLogo" alt="" class="dark-logo" />
                     <img :src="getMainLogo" class="light-logo" alt="" /></span> </router-link>
            </div>
            <div class="navbar-collapse">


                <ul class="navbar-nav mr-auto mt-md-0 ">
                    <!-- This is  -->
                    <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                    <li class="nav-item"> <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="icon-arrow-left-circle"></i></a> </li>
                    
                    
                    <li class="nav-item dropdown mega-dropdown"> <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-view-grid"></i></a>
                        <div class="dropdown-menu animated bounceInDown">
                            <ul class="mega-dropdown-menu row">
                                <li class="col-lg-3 col-xlg-2 m-b-30">
                                    <h4 class="m-b-20">CAROUSEL</h4>
                                    <!-- CAROUSEL -->
                                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner" role="listbox">
                                            <div class="carousel-item active">
                                                <div class="container"> <img class="d-block img-fluid" src="/images/big/img1.jpg" alt="First slide"></div>
                                            </div>
                                            <div class="carousel-item">
                                                <div class="container"><img class="d-block img-fluid" src="/images/big/img2.jpg" alt="Second slide"></div>
                                            </div>
                                            <div class="carousel-item">
                                                <div class="container"><img class="d-block img-fluid" src="/images/big/img3.jpg" alt="Third slide"></div>
                                            </div>
                                        </div>
                                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev"> <span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a>
                                        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next"> <span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="sr-only">Next</span> </a>
                                    </div>
                                    <!-- End CAROUSEL -->
                                </li>
                                <li class="col-lg-3 m-b-30">
                                    <h4 class="m-b-20">ACCORDION</h4>
                                    <!-- Accordian -->
                                    <div id="accordion" class="nav-accordion" role="tablist" aria-multiselectable="true">
                                        <div class="card">
                                            <div class="card-header" role="tab" id="headingOne">
                                                <h5 class="mb-0">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Collapsible Group Item #1
                                            </a>
                                            </h5> </div>
                                            <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                                                <div class="card-block"> Anim pariatur cliche reprehenderit, enim eiusmod high. </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header" role="tab" id="headingTwo">
                                                <h5 class="mb-0">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                Collapsible Group Item #2
                                            </a>
                                            </h5> </div>
                                            <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                                                <div class="card-block"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header" role="tab" id="headingThree">
                                                <h5 class="mb-0">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                Collapsible Group Item #3
                                            </a>
                                            </h5> </div>
                                            <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
                                                <div class="card-block"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3  m-b-30">
                                    <h4 class="m-b-20">CONTACT US</h4>
                                    <!-- Contact -->
                                    <form>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="exampleInputname1" placeholder="Enter Name"> </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" placeholder="Enter email"> </div>
                                        <div class="form-group">
                                            <textarea class="form-control" id="exampleTextarea" rows="3" placeholder="Message"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-info">Submit</button>
                                    </form>
                                </li>
                                <li class="col-lg-3 col-xlg-4 m-b-30">
                                    <h4 class="m-b-20">List style</h4>
                                    <!-- List style -->
                                    <ul class="list-style-none">
                                        <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> You can give link</a></li>
                                        <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> Give link</a></li>
                                        <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> Another Give link</a></li>
                                        <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> Forth link</a></li>
                                        <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> Another fifth link</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </li>

                       
                    <li class="nav-item hidden-sm-down">
                        <form class="app-search">
                            <input type="text" class="form-control" placeholder="Search for..."> <a class="srh-btn"><i class="ti-search"></i></a> </form>
                    </li>

                </ul>
                       
                <ul class="navbar-nav mr-auto mt-md-0 ">
                    <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="fas fa-bars"></i></a> </li>                    
                </ul>
                <ul class="navbar-nav my-lg-0">
                 

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-message"></i>
                            <div class="notify"  v-if="notifications.length > 0"> <span class="heartbit"></span> <span class="point"></span> </div>
                        </a>
                        <div class="dropdown-menu mailbox animated bounceInDown">
                            <ul>
                                <li>
                                    <div class="drop-title">Notifications</div>
                                </li>
                                <li>
                                    <div class="message-center">

                                        <a class="notification-item" v-for="item in notifications" :key="item.id" v-on:click="notificationClick(item, $event)">
                                            <div class="btn btn-danger btn-circle"><i class="fa fa-link"></i></div>
                                            <div class="mail-contnet">
                                                <h5>{{ item.user }}</h5> <span class="mail-desc">{{ item.text }}</span> <span class="time">{{ item.unid }} 9:30 AM</span>
                                            </div>
                                        </a>

                                    </div>
                                </li>
                                <li>
                                    <a class="nav-link text-center" href="javascript:void(0);"> <strong>Check all notifications</strong> <i class="fa fa-angle-right"></i> </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    

                    <li class="nav-item" v-tooltip.bottom="trans('todo.todo')" v-if="getConfig('todo') && hasPermission('access-todo')">
                        <router-link class="nav-link" to="/todo"><i class="far fa-check-circle"></i></router-link>
                    </li>
                    <li class="nav-item" v-tooltip.bottom="trans('message.message')" v-if="getConfig('message') && hasPermission('access-message')">
                        <router-link class="nav-link" to="/message"><i class="fas fa-envelope"></i></router-link>
                    </li>
                    <li class="nav-item dropdown" v-if="hasPermission('access-configuration') || hasPermission('list-department') || hasPermission('list-designation')">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-tooltip.bottom="trans('configuration.configuration')"><i class="fas fa-cogs"></i> </a>
                        <div :class="['dropdown-menu', getConfig('direction') != 'rtl' ? 'dropdown-menu-right' : '']">
                            <ul class="dropdown-user">
                                <li v-if="hasPermission('access-configuration')"><router-link to="/configuration"><i class="fas fa-cogs"></i> {{trans('configuration.configuration')}}</router-link></li>
                                <li v-if="hasPermission('access-configuration') && getConfig('backup')"><router-link to="/backup"><i class="fas fa-database"></i> {{trans('backup.backup')}}</router-link></li>
                                <li v-if="hasPermission('access-configuration') && getConfig('email_template')"><router-link to="/email-template"><i class="fas fa-envelope"></i> {{trans('template.email_template')}}</router-link></li>
                                <li v-if="hasPermission('access-configuration') && getConfig('email_log')"><router-link to="/email-log"><i class="fas fa-folder"></i> {{trans('mail.email_log')}}</router-link></li>
                                <li v-if="hasPermission('access-configuration') && getConfig('activity_log')"><router-link to="/activity-log"><i class="fas fa-bars"></i> {{trans('activity.activity_log')}}</router-link></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img :src="getAuthUser('avatar')" alt="user" class="profile-pic" /></a>
                        <div :class="['dropdown-menu', getConfig('direction') != 'rtl' ? 'dropdown-menu-right' : '']">
                            <ul class="dropdown-user">

                                <li>
                                    <div class="dw-user-box">
                                        <div class="u-img"><img :src="getAuthUser('avatar')" alt="user"></div>
                                        <div class="u-text">
                                            <h4>{{getAuthUser('full_name')}}</h4>
                                            <p class="text-muted">{{getAuthUser('email')}}</p><router-link to="/profile" class="btn btn-rounded btn-danger btn-sm">{{trans('user.view_profile')}}</router-link></div>
                                    </div>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li><router-link to="/change-password"><i class="fas fa-cogs"></i> {{trans('user.change_password')}}</router-link></li>                                
                                <li role="separator" class="divider"></li>
                                <li><a href="#" @click.prevent="logout"><i class="fas fa-power-off"></i> {{trans('auth.logout')}}</a></li>
                            </ul>
                        </div>
                    </li>

                   
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="flag-icon flag-icon-ar"></i></a>
                        <div class="dropdown-menu  dropdown-menu-right animated bounceInDown"> <a class="dropdown-item" href="#"><i class="flag-icon flag-icon-ar"></i> Español</a> <a class="dropdown-item" href="#"><i class="flag-icon flag-icon-us"></i> Inglés</a> </div>
                    </li>
                </ul>
            </div>
        </nav>

    </header>
</template>

<script>
    export default {
        data(){
            return {
                notifications: []
            }
        },

        mounted() {

            // console.log(process.env.MIX_APP_URL)

            Echo.channel('notifications')
                .listen('.newNotification', (notification) => {
                    notification.origin='push'; 
                    notification.markReadLink='/api/notification/' + notification.origin + '/' + notification.unid + '/' + 'read';  
                    this.notifications.push(notification);
                });

            axios.get('/api/notification/fetch/unread')
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
            logout(){
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login')
                })
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            getConfig(name){
                return helper.getConfig(name);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            hasRole(role){
                return helper.hasRole(role);
            },

            notificationClick(notification,event){
                if (event) event.preventDefault()

                // marcar como leido
                axios.get(notification.markReadLink)
                .then(response => response.data)
                .then(response => {                    
                    
                    if(response.message=='OK'){
                        this.notifications.splice(this.notifications.indexOf(notification), 1);
                    }
                    
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

                console.log(notification);
            }

        },
        computed: {
            getMainLogo(){
                if(helper.getConfig('main_logo'))
                    return '/'+helper.getConfig('main_logo');
                else
                    return '/images/default_main_logo.png';
            },
            getSidebarLogo(){
                if(helper.getConfig('sidebar_logo'))
                    return '/'+helper.getConfig('sidebar_logo');
                else
                    return '/images/default_sidebar_logo.png';
            }
        }
    }
</script>
