<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . '(18) NOT NULL',
            'username_is_temporary' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\'',
            'role' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',

            'items_uploaded' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',

            'name' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\'',
            'avatar_url' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\'',
            'url' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\'',
            'company' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\'',
            'location' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\'',

        ], $tableOptions);

        $this->createIndex('idx-user-name', '{{%user}}', 'username', true);
        $this->createIndex('idx-status', '{{%user}}', 'status');
        $this->createIndex('idx-items_uploaded', '{{%user}}', 'items_uploaded');

        $this->createTable(
            '{{%user_service}}',
            [
                'id' => Schema::TYPE_PK,
                'user_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
                'service_type' => Schema::TYPE_STRING . '(255) NOT NULL',
                'service_id' => Schema::TYPE_STRING . '(255) NOT NULL',
                'KEY `ix-user_service-user_id` (`user_id`)',
                'UNIQUE KEY `uq-user-service-service_type-service_id` (`service_type`, `service_id`)',
            ],
            $tableOptions
        );

        $this->createTable('{{%languages}}', [
            'id' => Schema::TYPE_PK,
            'name_en' => Schema::TYPE_STRING . ' NOT NULL',
            'name_native' => Schema::TYPE_STRING . ' NOT NULL',
            'language_code' => Schema::TYPE_STRING . '(5) NOT NULL',
            'sort_order' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ]);

        // structure - categories
        $this->createTable('{{%category}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
            'slug' => Schema::TYPE_STRING . '(120) NOT NULL',
        ], $tableOptions);

        // translations table for categoy
        $this->createTable('{{%category_lang}}', [
            'id' => Schema::TYPE_PK,
            'category_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
            'language' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NOT NULL',
        ]);
        $this->createIndex('idx-category-lang', '{{%category_lang}}', ['language', 'category_id'], true);

        // items!
        $this->createTable('{{%item}}', [
            'id' => Schema::TYPE_PK,
            'main_category_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
            'slug' => Schema::TYPE_STRING . '(120) NOT NULL',
            'owner' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'packagist_package_name' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\'',
            'github_repository' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\'',
            'license' => Schema::TYPE_STRING . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'min_dotplant_git_commit_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
        ], $tableOptions);

        $this->createIndex('idx-item-category-status', '{{%item}}', ['main_category_id', 'status']);

        // translations table for item
        $this->createTable('{{%item_lang}}', [
            'id' => Schema::TYPE_PK,
            'item_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
            'language' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NOT NULL',
        ]);
        $this->createIndex('idx-item-lang', '{{%item_lang}}', ['language', 'item_id'], true);

        // images for items

        $this->createTable('{{%item_images}}', [
            'id' => Schema::TYPE_PK,
            'item_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
            'image_original' => Schema::TYPE_STRING . ' NOT NULL',
            'image_resized' => Schema::TYPE_STRING . ' NOT NULL',
            'sort_order' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
        ]);
        $this->createIndex('idx-item-images', '{{%item_images}}', ['item_id', 'sort_order']);


        // tags support
        $this->createTable('{{%tag}}', [
            'id' => Schema::TYPE_PK,
            'frequency' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'name' => Schema::TYPE_STRING . '(120) NOT NULL',
            'moderated' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
        ]);
        $this->createIndex('idx-tag-name-uniq', '{{%tag}}', 'name', true);
        $this->createIndex('idx-moderated', '{{%tag}}', 'moderated');

        $this->createTable('{{%item_tag}}', [
            'item_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
            'tag_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
        ]);
        $this->addPrimaryKey('pk', '{{%item_tag}}', ['item_id', 'tag_id']);

        // application installs
        $this->createTable('{{%install_method}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ]);

        $this->batchInsert(
            '{{%install_method}}',
            ['name'],
            [
                ['unknown', ],
                ['git-clone', ],
                ['git-master-zip', ],
                ['devgroup', ],
                ['partner', ],
                ['site-archive', ],
                ['vagrant', ],
            ]
        );

        $this->createTable('{{%partner}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ]);

        $this->createTable('{{%user_partner}}', [
            'user_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
            'partner_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
        ]);
        $this->addPrimaryKey('pk', '{{%user_partner}}', ['user_id', 'partner_id']);

        $this->createTable('{{%install}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
            'unique_id' => Schema::TYPE_STRING . '(32) NOT NULL',
            'installed_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'last_ping_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'install_method_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL DEFAULT 1',
            'partner_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL DEFAULT 0',
            'git_version_commit' => Schema::TYPE_STRING . '(40) NOT NULL',
            'php_version' => Schema::TYPE_STRING . '(8) NOT NULL',
            'hostname' => Schema::TYPE_STRING . ' NOT NULL',
            'is_local_install' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
        ]);
        $this->createIndex('idx-local-installs', '{{%install}}', 'is_local_install');
        $this->createIndex('idx-by_user', '{{%install}}', 'user_id');
        $this->createIndex('idx-uniq', '{{%install}}', 'unique_id', true);



        $this->createTable('{{%install_items}}', [
            'id' => Schema::TYPE_PK,
            'install_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
            'item_id' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
            'installed_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'deleted_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'is_deleted' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
        ]);

        $this->createTable('{{%dotplant_git_commits}}', [
            'id' => Schema::TYPE_PK,
            'commited_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'commit_hash' => Schema::TYPE_STRING . '(40) NOT NULL DEFAULT \'\'',
        ]);
        
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%user_service}}');
        $this->dropTable('{{%languages}}');
        $this->dropTable('{{%category}}');
        $this->dropTable('{{%category_lang}}');
        $this->dropTable('{{%item}}');
        $this->dropTable('{{%item_lang}}');
        $this->dropTable('{{%item_images}}');
        $this->dropTable('{{%tag}}');
        $this->dropTable('{{%install_method}}');
        $this->dropTable('{{%partner}}');
        $this->dropTable('{{%user_partner}}');
        $this->dropTable('{{%install}}');
        $this->dropTable('{{%install_items}}');
        $this->dropTable('{{%dotplant_git_commits}}');

    }
}
