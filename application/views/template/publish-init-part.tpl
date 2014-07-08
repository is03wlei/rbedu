<div class="detailIn">
    <h6>选中学生名单</h6>
    <table>
        <thead><tr><th colspan="2">学生姓名</th></tr></thead>
        <tr>
        {%foreach name=stu from=$studentlist item=sname %}
        {%if $smarty.foreach.stu.index %2==0 && $smarty.foreach.stu.index != 0%}</tr><tr>{%/if%}<td>{%$sname%}</td>
        {%/foreach%}
        </tr>
    </table>
    <a style="display:none" href="javascript:void(0)">查看完整名单</a>
</div>
