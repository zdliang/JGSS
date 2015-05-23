package cn.net.jgss.xtpp;

import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.ViewPager;
import android.view.Menu;
import android.view.MenuItem;

import com.githang.viewpagerindicator.IconTabPageIndicator;

import java.util.ArrayList;
import java.util.List;


public class MainActivity extends FragmentActivity {

    private ViewPager mViewPager;
    private IconTabPageIndicator mIndicator;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        initViews();
    }

    private void initViews() {
        mViewPager = (ViewPager) findViewById(R.id.view_pager);
        mIndicator = (IconTabPageIndicator) findViewById(R.id.indicator);
        List<BaseFragment> fragments = initFragments();
        FragmentAdapter adapter = new FragmentAdapter(fragments, getSupportFragmentManager());
        mViewPager.setAdapter(adapter);
        mIndicator.setViewPager(mViewPager);
    }

    private List<BaseFragment> initFragments() {
        List<BaseFragment> fragments = new ArrayList<BaseFragment>();

        BaseFragment soldFragment = new BaseFragment();
        soldFragment.setTitle("何时卖");
        soldFragment.setIconId(R.drawable.tab_record_selector);
        fragments.add(soldFragment);

        BaseFragment buyFragment = new BaseFragment();
        buyFragment.setTitle("买什么");
        buyFragment.setIconId(R.drawable.tab_record_selector);
        fragments.add(buyFragment);

        BaseFragment taskFragment = new BaseFragment();
        taskFragment.setTitle("任务");
        taskFragment.setIconId(R.drawable.tab_record_selector);
        fragments.add(taskFragment);

        BaseFragment qaFragment = new BaseFragment();
        qaFragment.setTitle("问答");
        qaFragment.setIconId(R.drawable.tab_record_selector);
        fragments.add(qaFragment);

        BaseFragment userFragment = new BaseFragment();
        userFragment.setTitle("我");
        userFragment.setIconId(R.drawable.tab_user_selector);
        fragments.add(userFragment);

        return fragments;
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }
}
