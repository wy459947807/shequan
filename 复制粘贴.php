
<if condition="$status neq 1">
    <button class="btn btn-primary " type="submit">������</button>
    <else/>
    <a class="btn" >�����</a>
</if>

<notempty name="term">
</notempty>

 <notempty name="images['[type]']">
     <foreach name="images['[type]']" item="vo"></foreach>
 </notempty>

{:date('Y-m-d H:i:s',$vo['ctime'])}

<foreach name="course" item="vo"></foreach>

<a href="javascript:;" style="position: relative; display: inline-block;" >
    �ϴ���Ƭ
    <input type="button"   value="�ϴ���Ƭ"  id="up_img" style="opacity: 0; position: absolute; width:100%; height:100%; top:0; left:0;"  />
</a>
<input type="hidden" name="card_img" id="card_img" value=""/>

<script>
  window.onload = function () {
        //new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });
        initUploadImage("up_img","imgShow","card_img");//��ʼ��ͼƬ�ϴ�
    }
</script>

<?php

file_put_contents('1.txt', json_encode($token));

$model->table(C('DB_PREFIX') . 'users')->where(array("id"=>$params['uid']))->setDec("price",100);
$model->table(C('DB_PREFIX') . 'users')->where(array("id"=>$params['uid']))->setInc("price",100);
 $totalPrice=D("Home/UserSubscribe")->where(array("user_id"=>$params['uid']))->sum('price_total');//�ܺ�
$model->table(C('DB_PREFIX') . 'act_users')->where(array("condition"=>array(array("id" =>$params['id']),array("openid"=>$params['openid']),"or")))->save($updateArray);

round($orderItem['num']*$val['price'],2);

if(!empty($params['dateTime'])){
    $this->sqlWhere.=" and  to_days(last_reply_time) = to_days('%s') "; 
    $this->bindValues[] = $params['dateTime'];
}

$this->gift_model->where(array("id" => $id))->find();

$id = I('get.id', 0, 'intval');
$ids = I('post.ids/a');




if (!empty($params['start_time'])) {
    $this->sqlWhere .= " and  ctime > UNIX_TIMESTAMP('%s') ";
    $this->bindValues[] = $params['start_time'];
}

if (!empty($params['end_time'])) {
    $this->sqlWhere .= " and  ctime < UNIX_TIMESTAMP('%s') ";
    $this->bindValues[] = $params['end_time'];
}


if (!empty($params['keyword'])) {
    $this->sqlWhere .= " and  (name like '%s' or intro like '%s') ";
    $this->bindValues[] = "%" . $params['keyword'] . "%";
    $this->bindValues[] = "%" . $params['keyword'] . "%";
}


//workerman����
//
// ��ȡ�����������û��б� 
$clients_list = Gateway::getClientSessionsByGroup($room_id);
foreach($clients_list as $tmp_client_id=>$item){
    $clients_list[$tmp_client_id] = $item['client_name'];
}
$clients_list[$client_id] = $client_name;

//���뷿��
Gateway::joinGroup($client_id, $room_id);

//���乲��Ƶ��������Ϣ
Gateway::sendToGroup($room_id, json_encode($new_message));

//���Լ�������Ϣ
Gateway::sendToCurrentClient(json_encode($new_message));
//��ĳ�˷���Ϣ
Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));




var payInfo={
    body:dataInfo.order.order_name,
    orderNo:dataInfo.order.order_sn,
    total_fee:dataInfo.order.total_money,
    trade_type:"MWEB",
    scene_info:'{"h5_info":{"type":"Android","app_name":"ʮ��Ӯ����","package_name":"com.bm.shinianjinrong"}}',
}

var retData= getRemoteData(mergeArray(configInfo.tokenInfo,payInfo), configInfo.apiUrl+"Payment/wechatInfo",1);//�ύ����

//window.location.href="https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id="+retData.data.prepayid+"&package="+retData.data.package;



H5ԭ��
 <script type="text/javascript">
    document.addEventListener('plusready', function(){
            //console.log("����plus api��Ӧ���ڴ��¼���������ã���������plus is undefined��"

    });
</script>