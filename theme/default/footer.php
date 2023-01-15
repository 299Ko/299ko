<?php defined('ROOT') OR exit('No direct script access allowed'); ?>
            </div>
            <?php if(show::isPublicSidebar()) {
            ?>
            <aside id="sidebar">
                <?php
                show::displayPublicSidebar();
                ?>
            </aside><?php
        } ?>
        </div>
        <div id="footer">
            <div id="footer_content">
                <?php core::executeHookAction('footer'); ?>
                <p>
                    <a target='_blank' href='https://github.com/299ko/'>Just using 299ko</a> - Thème <?php show::theme(); ?> - <a rel="nofollow" href="<?php echo ADMIN_PATH ?>">Administration</a>
                </p>
                <?php core::executeHookAction('endFooter'); ?>
            </div>
        </div>
    </div>
<?php core::executeHookAction('endFrontBody'); ?>
</body>
</html>