import React from 'react'
import { mount } from 'enzyme'
import SurveyCompletedThankYou from '../../assets/js/content/SurveyCompletedThankYou'

test('SurveyCompletedThankYou contains a button to close it ', () => {
  const wrapper = mount(<SurveyCompletedThankYou />)
  const closeButton = wrapper.find('.close')
  expect(closeButton.text()).toBe('×')
})
