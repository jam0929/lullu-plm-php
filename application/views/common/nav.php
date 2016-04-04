<nav class="dockbar navbar-fixed-top" id="header-dockbar">
  <menu class="main-menu">
    <div class="container">
      <div class="row">
        <button id="open-close-menu" class="menu-btn btn-inverse pull-left"><span class="fui-list"></span></button>
        
        <a href="<?php echo base_url(); ?>" class="man-brand col-sm-5">
          <span class="logotype">Template</span>
          <span class="doc-title">website</span>
        </a>
        
        <dl class="col-sm-7 text-right">
            <dd>
                <a href="<?php echo base_url('about'); ?>" 
                    class="<?php echo current_url() == base_url('about') ? 'active' : ''; ?>">
                    <?php echo lang('nav_about'); ?>
                    
                </a>
            </dd>
            <dd>
                <a href="<?php echo base_url('how-it-works'); ?>" 
                    class="<?php echo current_url() == base_url('how-it-works') ? 'active' : ''; ?>">
                    <?php echo lang('nav_how_it_works'); ?>
                </a>
            </dd>
            <dd>
                <a href="<?php echo base_url('pricing'); ?>" 
                    class="<?php echo current_url() == base_url('pricing') ? 'active' : ''; ?>">
                    <?php echo lang('nav_pricing'); ?>
                </a>
            </dd>
            <?php if($this->auth->isLogin() == true) : ?>
            <dd>
                <a href="<?php echo base_url('user/modify'); ?>" 
                    class="<?php echo current_url() == base_url('user/modify') ? 'active' : ''; ?>">
                    <?php echo $this->session->userdata('username'); ?>
                </a>
            </dd>
            <dd>
                <a href="<?php echo base_url('user/logout'); ?>" 
                    class="<?php echo current_url() == base_url('user/logout') ? 'active' : ''; ?>">
                    <?php echo lang('user_logout'); ?>
                </a>
            </dd>
            <?php else : ?>
            <dd>
                <a href="<?php echo base_url('user/login'); ?>" 
                    class="<?php echo current_url() == base_url('user/login') ? 'active' : ''; ?>">
                    <?php echo lang('nav_log_in_sign_up'); ?>
                </a>
            </dd>
            <?php endif; ?>
            <dd>
                <a href="<?php echo base_url('help'); ?>" 
                    class="<?php echo current_url() == base_url('help') ? 'active' : ''; ?>">
                    <?php echo lang('nav_help'); ?>
                </a>
            </dd>
        </dl>
      </div>
    </div>
  </menu>
  <menu class="colapsed-menu">
    <dl>
        <dd>
            <a href="<?php echo base_url('about'); ?>" 
                class="<?php echo current_url() == base_url('about') ? 'active' : ''; ?>">
                <?php echo lang('nav_about'); ?>
            </a>
        </dd>
        <dd>
            <a href="<?php echo base_url('how-it-works'); ?>" 
                class="<?php echo current_url() == base_url('how-it-works') ? 'active' : ''; ?>">
                <?php echo lang('nav_how_it_works'); ?>
            </a>
        </dd>
        <dd>
            <a href="<?php echo base_url('pricing'); ?>" 
                class="<?php echo current_url() == base_url('how-it-works') ? 'active' : ''; ?>">
                <?php echo lang('nav_pricing'); ?>
            </a>
        </dd>
        <?php if($this->auth->isLogin() == true) : ?>
        <dd>
            <a href="<?php echo base_url('user/modify'); ?>" 
                class="<?php echo current_url() == base_url('user/modify') ? 'active' : ''; ?>">
                <?php echo $this->session->userdata('username'); ?>
            </a>
        </dd>
        <dd>
            <a href="<?php echo base_url('user/logout'); ?>" 
                class="<?php echo current_url() == base_url('user/logout') ? 'active' : ''; ?>">
                <?php echo lang('user_logout'); ?>
            </a>
        </dd>
        <?php else : ?>
        <dd>
            <a href="<?php echo base_url('user/login'); ?>" 
                class="<?php echo current_url() == base_url('user/login') ? 'active' : ''; ?>">
                <?php echo lang('nav_log_in_sign_up'); ?>
            </a>
        </dd>
        <?php endif; ?>
        <dd>
            <a href="<?php echo base_url('help'); ?>" 
                class="<?php echo current_url() == base_url('help') ? 'active' : ''; ?>">
                <?php echo lang('nav_help'); ?>
            </a>
        </dd>
    </dl>
  </menu>
</nav>

<div class="page-wrapper">
    <div class="container">