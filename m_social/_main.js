function snsCheck(key,use,conn)
{
	getIframeForAction('');
	if (conn == 'connect')
	{
		if (use == 'on' || use == 'off')
		{
			if (use == 'on')
			{
				if (!confirm('정말로 변경하시겠습니까?   '))
				{
					return false;
				}
			}
			frames.__iframe_for_action__.location.href = rooturl+'/?r='+raccount+'&m=social&a=disconnect&connect=Y&type='+key;
		}
		else {
			var w;
			var h;

			switch(key) 
			{
				case 't':
					w = 810;
					h = 550;
					break;
				case 'f':
					w = 1024;
					h = 680;
					break;
				case 'm':
					w = 900;
					h = 500;
					break;
				case 'y':
					w = 450;
					h = 450;
					break;
			}
			var url = rooturl+'/?r='+raccount+'&m=social&a=snscall_direct&type='+key;
			window.open(url,'','width='+w+'px,height='+h+'px,statusbar=no,scrollbars=no,toolbar=no');
		}
	}
	else if (conn == 'delete')
	{
		if (confirm('정말로 연결을 끊으시겠습니까?   '))
		{
			frames.__iframe_for_action__.location.href = rooturl+'/?r='+raccount+'&m=social&a=disconnect&delete=Y&type='+key;
		}
	}
	else {
		if (confirm('정말로 변경하시겠습니까?   '))
		{
			frames.__iframe_for_action__.location.href = rooturl+'/?r='+raccount+'&m=social&a=disconnect&type='+key;
		}
	}
}
