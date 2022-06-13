import React from 'react'
import { shallow, mount } from 'enzyme'
import LocaleReview from '../../assets/js/components/LocaleReview'

it('renders without crashing', () => {
  shallow(<LocaleReview />)
})

it('does not have a Vote component when showVote is false', () => {
  const wrapper = mount(<LocaleReview showVote={false} />)
  expect(wrapper.find('button.vote-button').length).toBe(0)
})

it('has a Vote component when showVote is true', () => {
  const wrapper = mount(<LocaleReview showVote={true} />)
  expect(wrapper.find('button.vote-button').length).toBe(1)
})
