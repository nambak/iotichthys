CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "organizations"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "owner" varchar not null,
  "business_register_number" varchar not null,
  "postcode" varchar,
  "address" varchar not null,
  "detail_address" varchar,
  "phone_number" varchar not null,
  "slug" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "organizations_business_register_number_unique" on "organizations"(
  "business_register_number"
);
CREATE UNIQUE INDEX "organizations_slug_unique" on "organizations"("slug");
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "resource" varchar not null,
  "action" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "permissions_resource_action_index" on "permissions"(
  "resource",
  "action"
);
CREATE UNIQUE INDEX "permissions_name_unique" on "permissions"("name");
CREATE UNIQUE INDEX "permissions_slug_unique" on "permissions"("slug");
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "is_system_role" tinyint(1) not null default '0',
  "scopeable_type" varchar,
  "scopeable_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "roles_scopeable_type_scopeable_id_index" on "roles"(
  "scopeable_type",
  "scopeable_id"
);
CREATE UNIQUE INDEX "roles_slug_unique" on "roles"("slug");
CREATE TABLE IF NOT EXISTS "teams"(
  "id" integer primary key autoincrement not null,
  "organization_id" integer not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("organization_id") references "organizations"("id") on delete cascade
);
CREATE UNIQUE INDEX "teams_slug_unique" on "teams"("slug");
CREATE TABLE IF NOT EXISTS "user_organizations"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "organization_id" integer not null,
  "is_owner" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("organization_id") references "organizations"("id") on delete cascade
);
CREATE UNIQUE INDEX "user_organizations_user_id_organization_id_unique" on "user_organizations"(
  "user_id",
  "organization_id"
);
CREATE TABLE IF NOT EXISTS "user_roles"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "role_id" integer not null,
  "roleable_type" varchar,
  "roleable_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade
);
CREATE INDEX "user_roles_roleable_type_roleable_id_index" on "user_roles"(
  "roleable_type",
  "roleable_id"
);
CREATE UNIQUE INDEX "user_role_scope_unique" on "user_roles"(
  "user_id",
  "role_id",
  "roleable_id",
  "roleable_type"
);
CREATE TABLE IF NOT EXISTS "user_teams"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "team_id" integer not null,
  "is_leader" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("team_id") references "teams"("id") on delete cascade
);
CREATE UNIQUE INDEX "user_teams_user_id_team_id_unique" on "user_teams"(
  "user_id",
  "team_id"
);
CREATE TABLE IF NOT EXISTS "role_permissions"(
  "id" integer primary key autoincrement not null,
  "role_id" integer not null,
  "permission_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("role_id") references "roles"("id") on delete cascade,
  foreign key("permission_id") references "permissions"("id") on delete cascade
);
CREATE UNIQUE INDEX "role_permissions_role_id_permission_id_unique" on "role_permissions"(
  "role_id",
  "permission_id"
);
CREATE TABLE IF NOT EXISTS "permission_user"(
  "id" integer primary key autoincrement not null,
  "permission_id" integer not null,
  "user_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "permission_user_permission_id_user_id_unique" on "permission_user"(
  "permission_id",
  "user_id"
);
CREATE TABLE IF NOT EXISTS "categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "parent_id" integer,
  "sort_order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("parent_id") references "categories"("id") on delete cascade
);
CREATE INDEX "categories_parent_id_sort_order_index" on "categories"(
  "parent_id",
  "sort_order"
);
CREATE INDEX "categories_is_active_index" on "categories"("is_active");
CREATE INDEX "categories_slug_index" on "categories"("slug");
CREATE UNIQUE INDEX "categories_slug_unique" on "categories"("slug");
CREATE TABLE IF NOT EXISTS "category_access_controls"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "category_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("category_id") references "categories"("id") on delete cascade
);
CREATE UNIQUE INDEX "category_access_controls_user_id_category_id_unique" on "category_access_controls"(
  "user_id",
  "category_id"
);
CREATE INDEX "category_access_controls_category_id_user_id_index" on "category_access_controls"(
  "category_id",
  "user_id"
);

INSERT INTO migrations VALUES(133,'2025_08_01_193000_change_users_to_use_soft_deletes',1);
INSERT INTO migrations VALUES(145,'0001_01_01_000000_create_users_table',2);
INSERT INTO migrations VALUES(146,'0001_01_01_000001_create_cache_table',2);
INSERT INTO migrations VALUES(147,'0001_01_01_000002_create_jobs_table',2);
INSERT INTO migrations VALUES(148,'2025_05_16_102846_create_organizations_table',2);
INSERT INTO migrations VALUES(149,'2025_05_16_102846_create_permissions_table',2);
INSERT INTO migrations VALUES(150,'2025_05_16_102846_create_roles_table',2);
INSERT INTO migrations VALUES(151,'2025_05_16_102846_create_teams_table',2);
INSERT INTO migrations VALUES(152,'2025_05_16_102846_create_user_organizations_table',2);
INSERT INTO migrations VALUES(153,'2025_05_16_102846_create_user_roles_table',2);
INSERT INTO migrations VALUES(154,'2025_05_16_102846_create_user_teams_table',2);
INSERT INTO migrations VALUES(155,'2025_05_16_102847_create_role_permissions_table',2);
INSERT INTO migrations VALUES(156,'2025_08_11_162111_create_permission_user_table',3);
INSERT INTO migrations VALUES(157,'2024_08_11_180000_create_categories_table',4);
INSERT INTO migrations VALUES(158,'2025_08_13_113829_create_category_access_controls_table',5);
