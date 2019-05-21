<?php
//定义语言常量
define("RECORD_COUNT", '条记录'); //条记录
define("PREV_PAGE", '上一页'); //上一页
define("NEXT_PAGE", '下一页'); //下一页
define("FIRST_PAGE", '首页'); //第一页
define("LAST_PAGE", '尾页'); //最后一页
define("PAGED", 'p'); //分页页码参数
define("ROLLPAGE", 5); //分页栏每页显示的页数
define("ORGION_FORMAT", "%upPage% %linkPage% %downPage%");
class Page
{
	// 起始行数
	public $firstRow;
	// 列表每页显示行数
	public $listRows;
	// 页数跳转时要带的参数
	public $parameter;
	//分页所带的锚点
	public $anchor;
	//当前数据开始行
	public $offset = 0;
	// 分页总页面数
	public $totalPages;
	// 总行数
	protected $totalRows;
	// 当前页数
	public $nowPage;
	// 传参页数
	public $p;
	// 分页的栏的总页数
	protected $coolPages;
	// 分页栏每页显示的页数
	protected $rollPage = ROLLPAGE;
	// 分页参数名称
	protected $paged = PAGED;
	
	// 分页显示定制
	protected $config = array (
			'header' => RECORD_COUNT,
			'prev' => PREV_PAGE,
			'next' => NEXT_PAGE,
			'first' => FIRST_PAGE,
			'last' => LAST_PAGE,
			'theme' => ORGION_FORMAT
	);

	/**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int $totalRows  总的记录数
     * @param int $listRows  每页显示记录数
     * @param string $parameter  分页跳转的参数
     * @param string $anchor 锚点
     +----------------------------------------------------------
     */
	public function __construct($totalRows, $listRows, $parameter = '', $anchor = '')
	{
		$this->totalRows = (int) $totalRows;
		$this->parameter = $parameter;
		//$this->rollPage = '';
		//$this->paged = '';
		$this->anchor = $anchor ? $anchor : '';
		$this->listRows = (int) $listRows;
		$this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
		$this->coolPages = ceil($this->totalPages / $this->rollPage);
		$this->nowPage = ! empty($_REQUEST[$this->paged]) ? $_REQUEST[$this->paged] : 1;
		$this->p = $this->nowPage;
		if (! empty($this->totalPages) && $this->nowPage > $this->totalPages) {
			$this->nowPage = $this->totalPages;
		}
		$this->firstRow = $this->listRows * ($this->nowPage - 1);
	}

	public function setConfig($name, $value)
	{
		if (isset($this->config[$name])) {
			$this->config[$name] = $value;
		}
	}

	/**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
	public function show($jsFunction = null)
	{
		if (0 == $this->totalRows) {
			return '';
		}

		$nowCoolPage = ceil($this->nowPage / $this->rollPage);
		/*if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
			$parse = parse_url($_SERVER['HTTP_X_REWRITE_URL']);
		} else {
        	$parse = parse_url(getCurrentPageUrl());
		}
        if (isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$this->paged]);
			$url = $parse['path'].'?'.http_build_query($params);
		} else {
			$url = $parse['path'];
		}*/
		$url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : "?") . $this->parameter;
		$parse = parse_url($url);
		if (isset($parse['query'])) {
			parse_str($parse['query'], $params);
			unset($params[$this->paged]);
			$url = $parse['path'] . '?' . http_build_query($params);
		}
		//上下翻页字符串
		$upRow = $this->nowPage - 1;
		$downRow = $this->nowPage + 1;
		if ($upRow > 0) {
			$theFirst = '<a href="' . $url . '&' . $this->paged . '=1' . $this->anchor . '" class="first">' . $this->config['first'] . '</a>';
			if ($jsFunction) {
				$upPage = '<a href="javascript:'.$jsFunction.'('.$upRow.');" class="pre">' . $this->config['prev'] . '</a>';
			} else {
				$upPage = '<a href="' . $url . '&' . $this->paged . '=' . $upRow.$this->anchor . '" class="pre">' . $this->config['prev'] . '</a>';
			}

		} else {
			$theFirst = '<a class="first">' . $this->config['first'] . '</a>';
			$upPage = '<a class="pre selected">' . $this->config['prev'] . '</a>';
		}

		if ($downRow <= $this->totalPages) {
			$theEnd = '<a href="' . $url . '&' . $this->paged . '=' . $this->totalPages.$this->anchor . '" class="last">' . $this->config['last'] . '</a>';
				if ($jsFunction) {
					$downPage = '<a href="javascript:'.$jsFunction.'('.$downRow.');" class="pre">' . $this->config['next'] . '</a>';
				} else {
					$downPage = '<a href="' . $url . '&' . $this->paged . '=' . $downRow.$this->anchor . '" class="next">' . $this->config['next'] . '</a>';
				}
			} else {
			$theEnd = '<a class="last">' . $this->config['last'] . '</a>';
			$downPage = '<a class="next selected">' . $this->config['next'] . '</a>';
		}
		// 1 2 3 4 5
		$linkPage = "";
		/* for($i = 1; $i <= $this->rollPage; $i ++) {
			$page = ($nowCoolPage - 1) * $this->rollPage + $i;
			if ($page != $this->nowPage) {
				if ($page <= $this->totalPages) {
					if ($jsFunction) {
						$linkPage .= '<a href="javascript:'.$jsFunction.'('.$page.');" class="">' . $page . '</a>';
					} else {
						$linkPage .= '<a href="' . $url . '&' . $this->paged . '='.$page.$this->anchor.'" class="">' . $page . '</a>';
					}
				} else {
					break;
				}
			} else {
				if ($this->totalPages != 1) {
					$linkPage .= '<a class="selected">' . $page . '</a>';
				}
			}
		} */
		$nowDiffPage = $this->nowPage - ceil($this->rollPage / 2);
		$nowDiffPage = $nowDiffPage < 1 ? 0 : $nowDiffPage;
		for($i = 1; $i <= $this->rollPage; $i ++) {
			$page = $nowDiffPage + $i;
			if ($page != $this->nowPage) {
				if ($page <= $this->totalPages) {
					if ($jsFunction) {
						$linkPage .= '<a href="javascript:'.$jsFunction.'('.$page.');" class="">' . $page . '</a>';
					} else {
						$linkPage .= '<a href="' . $url . '&' . $this->paged . '='.$page.$this->anchor.'" class="">' . $page . '</a>';
					}
				} else {
					break;
				}
			} else {
				if ($this->totalPages != 1) {
					$linkPage .= '<a class="selected">' . $page . '</a>';
				}
			}
		}

		$pageStr = '';
		if ($linkPage) {
			$pageStr = str_replace(array (
					'%upPage%',
					'%linkPage%',
					'%downPage%',
			), array (
					$upPage,
					$linkPage,
					$downPage,
			), $this->config['theme']);
		}
		return '<div class="pageDiv">'.$pageStr.'</div>';
	}
	/**
	 +----------------------------------------------------------
	 * 分页显示输出
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 */
	public function showJs($currPage = 1)
	{
		if (0 == $this->totalRows) {
			return '';
		}
		$currPage = (int)$currPage;
		if ($currPage <= 0) {
			$currPage = 1;
		}
		if ($currPage > $this->totalPages) {
			$currPage = $this->totalPages;
		}
		//上下翻页字符串
		$upRow = $currPage - 1;
		$downRow = $currPage + 1;
		if ($upRow > 0) {
			$upPage = '<a href="javascript:;" class="g_page" page="'.$upRow.'">' . $this->config['prev'] . '</a>';
		} else {
			$upPage = '<a >' . $this->config['prev'] . '</a>';
		}

		if ($downRow <= $this->totalPages) {
			$downPage = '<a href="javascript:;" class="g_page" page="'.$downRow.'">' . $this->config['next'] . '</a>';
		} else {
			$downPage = '<a >' . $this->config['next'] . '</a>';
		}
		// 1 2 3 4 5
		$linkPage = "";

		$nowDiffPage = $currPage - ceil($this->rollPage / 2);
		$nowDiffPage = $nowDiffPage < 1 ? 0 : $nowDiffPage;
		for($i = 1; $i <= $this->rollPage; $i ++) {
			$page = $nowDiffPage + $i;
			if ($page != $currPage) {
				if ($page <= $this->totalPages) {
					$linkPage .= '<a href="javascript:;" class="g_page" page="'.$page.'">' . $page . '</a>';
				} else {
					break;
				}
			} else {
				if ($this->totalPages != 1) {
					$linkPage .= '<a class="selected">' . $page . '</a>';
				}
			}
		}

		$pageStr = '';
		if ($linkPage) {
			$pageStr = str_replace(array (
					'%upPage%',
					'%linkPage%',
					'%downPage%',
			), array (
					$upPage,
					$linkPage,
					$downPage,
			), $this->config['theme']);
		}
		return '<div>'.$pageStr.'</div>';
	}
	
	/**
	 +----------------------------------------------------------
	 * 分页显示输出
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 */
	public function showAdmin($currPage = 1)
	{
		
		if (0 == $this->totalRows) {
			return '';
		}
		$currPage = (int)$currPage;
		if ($currPage <= 0) {
			$currPage = 1;
		}
		if ($currPage > $this->totalPages) {
			$currPage = $this->totalPages;
		}
		
		$this->offset = $this->listRows * ( $currPage - 1 );
		
		//上下翻页字符串
		$upRow = $currPage - 1;
		$downRow = $currPage + 1;
		if ($upRow > 0) {
			$upPage = '<a href="javascript:toPage('.$upRow.');" class="page" page="'.$upRow.'">' . $this->config['prev'] . '</a>';
		} else {
			$upPage = '<a >' . $this->config['prev'] . '</a>';
		}

		if ($downRow <= $this->totalPages) {
			$downPage = '<a href="javascript:toPage('.$downRow.');" class="page" page="'.$downRow.'">' . $this->config['next'] . '</a>';
		} else {
			$downPage = '<a >' . $this->config['next'] . '</a>';
		}
		// 1 2 3 4 5
		$linkPage = "";

		$nowDiffPage = $currPage - ceil($this->rollPage / 2);
		$nowDiffPage = $nowDiffPage < 1 ? 0 : $nowDiffPage;
		for($i = 1; $i <= $this->rollPage; $i ++) {
			$page = $nowDiffPage + $i;
			if ($page != $currPage) {
				if ($page <= $this->totalPages) {
					$linkPage .= '<a href="javascript:toPage('.$page.');" class="g_page" page="'.$page.'">' . $page . '</a>';
				} else {
					break;
				}
			} else {
				if ($this->totalPages != 1) {
					$linkPage .= '<a class="active">' . $page . '</a>';
				}
			}
		}

		$pageStr = '';
		if ($linkPage) {
			$pageStr = str_replace(array (
					'%upPage%',
					'%linkPage%',
					'%downPage%',
			), array (
					$upPage,
					$linkPage,
					$downPage,
			), $this->config['theme']);
		}
		
		//自定义页面跳转
		if($this->totalPages != 1){
			$toPageStr = '<a style="background:none;border:0px;line-height:29px;margin-right:10px;"><input type="text" id="pageNum" style="width:40px;height:28px;text-align:center; margin-right:5px; border:1px solid #008DB6;" value="'.$currPage.'" /> / '.$this->totalPages.'页</a>';
		}else{
			$toPageStr = '';
		}
		
		//记录数
		$totalRowsStr = '<a style="background:none;border:0px;line-height:29px;margin-right:10px;">共'.$this->totalRows.'条记录</a>';
		
		
		return '<div class="paging-wrp"><div class="paging">'.$totalRowsStr.$pageStr.$toPageStr.'</div></div>';
	}
}
