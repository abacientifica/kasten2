<template>
    <div>
        <navbar></navbar>
        <sidebar></sidebar>
        <div class="content-wrapper">
            <transition name="slide" mode="out-in">
                <router-view></router-view>
            </transition>
        </div>
        <Footer></Footer>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
</template>
<script>
export default {
    
}
</script>