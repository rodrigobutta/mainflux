<template>

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

</template>

<script>

    export default {
      
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
            
        }        
    }
</script>
