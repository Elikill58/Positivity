<?php

class Lang
{
    public $array = [];

    public function __construct()
    {
        $array = array();
        $this->array = &$array;

        $array["yes"] = "是";
        $array["no"] = "不";

        $array["index.main"] = "欢迎来到%server%的Negativity网页接口";
        $array["index.sub"] = "在这里，列出了所有禁令、Negativity帐户和验证";
        $array["index.empty"] = "更多使用 Negativity 来填充数据。";
        $array["index.version.pull.try"] = "尝试更新到最新版本";
        $array["index.version.no.title"] = "你是最新的版本！";
        $array["index.version.no.sub"] = "您正在使用最新的 Positivity 版本 :)";
        $array["index.version.snapshot.title"] = "可用快照版本！";
        $array["index.version.snapshot.snapshot_too"] = "您可以尝试更新到最新的快照版本。";
        $array["index.version.snapshot.upgrade"] = "你正在使用快照版本。公开版本: %version%. 实际上你正在使用 %actual_version%";
        $array["index.version.snapshot.release"] = "你正在使用快照版本，有新版本可用: %version%。";
        $array["index.version.yes.title"] = "一个新版本可用";
        $array["index.version.yes.sub"] = "更新的版本：%version%. 你正在使用的版本：%actual_version%";

        $array["accounts.empty"] = "暂时还没有帐户";
        $array["bans.empty"] = "暂时还没有账户被封禁";
        $array["bans_logs.empty"] = "暂时还没有账户被封禁过";
        $array["admin_roles.empty"] = "暂时还没有角色，你可以在此页面创建";
        $array["proofs.empty"] = "暂时还有没有玩家作弊的证据";

        $array["connection.name"] = "用你的账户连接到Negativity";
        $array["connection.form.login"] = "用户名";
        $array["connection.form.password"] = "密码";
        $array["connection.form.confirm"] = "连接";
        $array["connection.wrong_name"] = "登陆失败！";
        $array["connection.wrong_pass"] = "用户名或密码错误！";
        $array["connection.well"] = "连接成功！";
        $array["connection.back"] = "点击返回首页";
        $array["connection.disconnect"] = "断开连接";
        $array["connection.login_as"] = "当前用户：%name%";

        $array["title.index"] = "首页";
        $array["title.bans"] = "封禁";
        $array["title.bans_logs"] = "封禁记录";
        $array["title.accounts"] = "账户";
        $array["title.proofs"] = "证据";
        $array["title.admin_users"] = "管理员用户";
        $array["title.admin_roles"] = "管理员角色";
        $array["title.verifications"] = "验证";
        $array["title.search"] = "搜索";
        $array["title.connection"] = "连接";

        $array["generic.cheat"] = "作弊";
        $array["generic.amount"] = "次数";
        $array["generic.name"] = "名称：%name%";
        $array["generic.lang"] = "语言：%lang%";
        $array["generic.creation_time"] = "首次进服时间: %time%";
        $array["generic.uuid"] = "UUID: %uuid%";
        $array["generic.see_more"] = "查看更多";
        $array["generic.see_all"] = "查看全部";
        $array["generic.delete"] = "删除";
        $array["generic.clear"] = "清空";
        $array["generic.save"] = "保存";
        $array["generic.ban"] = "封禁";
        $array["generic.unban"] = "解封";
        $array["generic.never"] = "用不";
        $array["generic.expired"] = "已过期";

        $array["ask.ban.reason"] = "封禁理由";

        $array["column.id"] = "UUID";
        $array["column.name"] = "玩家名称";
        $array["column.role_name"] = "角色名称";
        $array["column.lang"] = "语言";
        $array["column.verifications"] = "验证次数";
        $array["column.minerate_full"] = "开采的方块总数";
        $array["column.most_clicks"] = "最打点击数/秒";
        $array["column.violations"] = "违规总数";
        $array["column.reason"] = "封禁理由";
        $array["column.banned_by"] = "被谁封禁";
        $array["column.expiration_time"] = "到期时间";
        $array["column.revocation_time"] = "撤销时间";
        $array["column.creation_time"] = "创建时间";
        $array["column.cheat_name"] = "作弊类型";
        $array["column.started_by"] = "开始于";
        $array["column.player_version"] = "玩家版本";
        $array["column.amount"] = "数量";
        $array["column.check_name"] = "检查名称";
        $array["column.reliability"] = "可靠性";
        $array["column.ping"] = "延迟";
        $array["column.time"] = "时间";
        $array["column.more_info"] = "更多信息";
        $array["column.user_name"] = "用户名";
        $array["column.password"] = "密码";
        $array["column.is_admin"] = "是否为管理员";
        $array["column.special"] = "特殊";
        $array["column.options"] = "操作";
        $array["column.bans"] = "封禁";
        $array["column.bans_logs"] = "封禁记录";
        $array["column.accounts"] = "账户";
        $array["column.proofs"] = "证据";
        $array["column.admin_users"] = "管理员用户";
        $array["column.admin_roles"] = "管理员角色";

        $array["table.pager.number"] = "页";

        $array["check.violations"] = "违规行为";
        $array["check.minerate"] = "采矿统计";
        $array["check.bans"] = "封禁 (%nb%)";
        $array["check.bans_logs"] = "封禁记录 (%nb%)";
        $array["check.verifications"] = "验证 (%nb%)";
        $array["check.proofs"] = "证据 (%nb%)";
        $array["check.propositions"] = "以下一些可能是满足您要求的玩家";

        $array["minerate.name"] = "采矿统计";
        $array["minerate.ancient_debris"] = "远古残骸";
        $array["minerate.diamond_ore"] = "钻石矿";
        $array["minerate.gold_ore"] = "金矿";
        $array["minerate.iron_ore"] = "铁矿";
        $array["minerate.coal_ore"] = "煤矿";
        $array["minerate.all"] = "所有其他方块";

        $array["verifications.result"] = "验证结果";
        $array["verifications.empty"] = "暂时还没有任何验证信息";

        $array["admin.create_user"] = "创建一个新用户";
        $array["admin.create_roles"] = "创建一个新角色";
        $array["admin.duplicate"] = "这个用户已经存在了";
        $array["admin.button.create_user"] = "创建用户";
        $array["admin.button.create_roles"] = "创建角色";
        $array["admin.special.nothing"] = "基础的";
        $array["admin.special.un_removable"] = "不能被删除的";

        $array["role.none"] = "无";
        $array["role.see"] = "看";
        $array["role.edit"] = "编辑";
        $array["role.manage"] = "管理";

        $array["error.not_found.player"] = "没有找到该玩家。";
        $array["error.not_found.verifications"] = "没有找到该验证";

        $array["server_crash"] = "服务器崩溃";
        $array["world_downloader"] = "世界下载器";
    }
}
