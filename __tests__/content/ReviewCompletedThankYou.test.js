import React from 'react';
import { mount } from 'enzyme';
import ReviewCompletedThankYou from "../../assets/js/content/ReviewCompletedThankYou";

test('ReviewCompletedThankYou contains a button to close it ', () => {
    const wrapper = mount(<ReviewCompletedThankYou />);
    const closeButton = wrapper.find('.close');
    expect(closeButton.text()).toBe('×');
});