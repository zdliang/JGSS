package cn.net.jgss.xtpp;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;

import com.githang.viewpagerindicator.IconPagerAdapter;

import java.util.List;

/**
 * Created by daliangz on 5/23/2015.
 */
public class FragmentAdapter extends FragmentPagerAdapter implements IconPagerAdapter {

    private List<BaseFragment> mFragments;

    public FragmentAdapter(List<BaseFragment> fragments, FragmentManager fm) {
        super(fm);
        mFragments = fragments;
    }

    @Override
    public Fragment getItem(int i) {
        return mFragments.get(i);
    }

    @Override
    public int getIconResId(int index) {
        return mFragments.get(index).getIconId();
    }

    @Override
    public int getCount() {
        return mFragments.size();
    }

    @Override
    public CharSequence getPageTitle(int position) {
        return mFragments.get(position).getTitle();
    }
}
